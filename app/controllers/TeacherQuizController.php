<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Middleware\AuthMiddleware;
use App\Middleware\CSRFMiddleware;
use App\Middleware\RoleMiddleware;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Repositories\QuizRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\AnswerRepository;
use App\Repositories\CategoryRepository;

class TeacherQuizController extends BaseController
{
    private QuizRepository $quizzes;
    private QuestionRepository $questions;
    private AnswerRepository $answers;
    private CategoryRepository $categories;

    public function __construct($db)
    {
        $this->quizzes = new QuizRepository($db);
        $this->questions = new QuestionRepository($db);
        $this->answers = new AnswerRepository($db);
        $this->categories = new CategoryRepository($db);
    }

    /* ===================== LISTE DES QUIZ ===================== */

    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');

        $quizzes = $this->quizzes->findByEnseignant(Session::getUserId());

        $this->view('teacher/quiz/index', [
            'quizzes' => $quizzes,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

    /* ===================== CRÉER UN QUIZ ===================== */

    public function showCreate(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');

        $enseignantId = Session::getUserId();
        $categories = $this->categories->findByEnseignant($enseignantId);

        if (empty($categories)) {
            Session::setError("Vous devez créer une catégorie avant de créer un quiz.");
            $this->redirect('/teacher/categories/create');
        }


        $this->view('teacher/quiz/create', [
            'categories' => $categories,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

    public function create(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');
        CSRFMiddleware::handle();

        $data = [
            'title' => Security::sanitize($_POST['title'] ?? ''),
            'description' => Security::sanitize($_POST['description'] ?? ''),
            'categorie_id' => (int) ($_POST['categorie_id'] ?? 0),
            'status' => Security::sanitize($_POST['status'] ?? 'actif'),
            'duration' => (int) ($_POST['duration'] ?? 30),
        ];

        if ($data['categorie_id'] <= 0) {
            Session::setError("Catégorie invalide.");
            $this->redirect('/teacher/quizzes/create');
        }
        $validator = new Validator();
        if (
            !$validator->validate($data, [
                'title' => ['required', 'min:3', 'max:200'],
                'description' => ['max:1000'],
                'duration' => ['required', 'numeric']
            ])
        ) {
            Session::setError('Données invalides');
            $this->redirect('/teacher/quizzes/create');
        }

        $quiz = new Quiz($data);
        $quiz->enseignant_id = Session::getUserId();
        $quizId = $this->quizzes->create($quiz);

        // Création des questions et réponses
        $questions = $_POST['questions'] ?? [];
        foreach ($questions as $index => $qData) {
            $question = new Question([
                'quiz_id' => $quizId,
                'text' => Security::sanitize($qData['text'] ?? ''),
                'type_question' => Security::sanitize($qData['type_question'] ?? 'qcm'),
                'points' => (int) ($qData['points'] ?? 1),
                'ordre' => $index
            ]);
            $questionId = $this->questions->create($question);

            $answers = $qData['answers'] ?? [];
            foreach ($answers as $answerData) {
                $answer = new Answer([
                    'question_id' => $questionId,
                    'text' => Security::sanitize($answerData['text'] ?? ''),
                    'is_correct' => !empty($answerData['is_correct'])
                ]);
                $this->answers->create($answer);
            }
        }

        Session::setSuccess("Quiz créé avec succès.");
        $this->redirect('/teacher/quizzes');
    }

    /* ===================== MODIFIER UN QUIZ ===================== */

    public function showEdit(int $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');

        $quiz = $this->quizzes->findById($id);

        if (!$quiz || $quiz->enseignant_id !== Session::getUserId()) {
            Session::setError("Quiz introuvable ou accès refusé.");
            $this->redirect('/teacher/quizzes');
        }

        $enseignantId = Session::getUserId();
        $categories = $this->categories->findByEnseignant($enseignantId);

        // Récupérer les questions et réponses du quiz
        $questions = $this->questions->findByQuiz($id);

        // Pour chaque question, récupérer ses réponses
        $questionsWithAnswers = [];
        foreach ($questions as $question) {
            $answers = $this->answers->findByQuestion($question->id);
            $questionsWithAnswers[] = [
                'question' => $question,
                'answers' => $answers
            ];
        }

        $this->view('teacher/quiz/edit', [
            'quiz' => $quiz,
            'categories' => $categories,
            'questionsWithAnswers' => $questionsWithAnswers,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

    public function update(int $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');
        CSRFMiddleware::handle();

        $quiz = $this->quizzes->findById($id);

        if (!$quiz || $quiz->enseignant_id !== Session::getUserId()) {
            Session::setError("Quiz introuvable ou accès refusé.");
            $this->redirect('/teacher/quizzes');
        }

        $data = [
            'title' => Security::sanitize($_POST['title'] ?? ''),
            'description' => Security::sanitize($_POST['description'] ?? ''),
            'categorie_id' => (int) ($_POST['categorie_id'] ?? 1),
            'status' => Security::sanitize($_POST['status'] ?? 'actif'),
            'duration' => (int) ($_POST['duration'] ?? 30),
        ];

        // Validation
        $validator = new Validator();
        if (
            !$validator->validate($data, [
                'title' => ['required', 'min:3', 'max:200'],
                'description' => ['max:1000'],
                'status' => ['required', 'in:actif,inactif,archive'],
                'duration' => ['required', 'numeric']
            ])
        ) {
            Session::setError('Données invalides');
            $this->redirect("/teacher/quizzes/edit/{$id}");
        }

        // Mise à jour du quiz
        $quiz->title = $data['title'];
        $quiz->description = $data['description'];
        $quiz->categorie_id = $data['categorie_id'];
        $quiz->status = $data['status'];
        $quiz->duration = $data['duration'];

        $this->quizzes->update($quiz);

        // Mise à jour des questions (optionnel : vous pouvez implémenter une logique plus complexe)
        // Pour simplifier, on peut supprimer les anciennes questions et en créer de nouvelles
        $existingQuestions = $this->questions->findByQuiz($id);
        foreach ($existingQuestions as $oldQuestion) {
            // Supprimer les réponses de la question
            $oldAnswers = $this->answers->findByQuestion($oldQuestion->id);
            foreach ($oldAnswers as $oldAnswer) {
                $this->answers->delete($oldAnswer->id);
            }
            // Supprimer la question
            $this->questions->delete($oldQuestion->id);
        }

        // Créer les nouvelles questions
        $questions = $_POST['questions'] ?? [];
        foreach ($questions as $index => $qData) {
            $question = new Question([
                'quiz_id' => $id,
                'text' => Security::sanitize($qData['text'] ?? ''),
                'type_question' => Security::sanitize($qData['type_question'] ?? 'qcm'),
                'points' => (int) ($qData['points'] ?? 1),
                'ordre' => $index
            ]);
            $questionId = $this->questions->create($question);

            $answers = $qData['answers'] ?? [];
            foreach ($answers as $answerData) {
                $answer = new Answer([
                    'question_id' => $questionId,
                    'text' => Security::sanitize($answerData['text'] ?? ''),
                    'is_correct' => !empty($answerData['is_correct'])
                ]);
                $this->answers->create($answer);
            }
        }

        Session::setSuccess("Quiz mis à jour avec succès.");
        $this->redirect('/teacher/quizzes');
    }

    /* ===================== SUPPRIMER UN QUIZ ===================== */

    public function delete(int $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');
        CSRFMiddleware::handle();

        $quiz = $this->quizzes->findById($id);

        if (!$quiz || $quiz->enseignant_id !== Session::getUserId()) {
            Session::setError("Quiz introuvable ou accès refusé.");
            $this->redirect('/teacher/quizzes');
        }

        // Supprimer les réponses de toutes les questions du quiz
        $questions = $this->questions->findByQuiz($id);
        foreach ($questions as $question) {
            $answers = $this->answers->findByQuestion($question->id);
            foreach ($answers as $answer) {
                $this->answers->delete($answer->id);
            }
            $this->questions->delete($question->id);
        }

        // Supprimer le quiz (soft delete)
        $this->quizzes->delete($id);

        Session::setSuccess("Quiz supprimé avec succès.");
        $this->redirect('/teacher/quizzes');
    }
}