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
use App\Repositories\CategoryRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\AnswerRepository;

class TeacherQuizController extends BaseController
{
    private QuizRepository $quizzes;
    private CategoryRepository $categories;
    private QuestionRepository $questions;
    private AnswerRepository $answers;

    public function __construct($db)
    {
        $this->quizzes = new QuizRepository($db);
        $this->categories = new CategoryRepository($db);
        $this->questions = new QuestionRepository($db);
        $this->answers = new AnswerRepository($db);
    }

    public function index()
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

    public function showCreate()
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');
        $categories = $this->categories->findByEnseignant(Session::getUserId());
        $this->view('teacher/quiz/create', [
            'categories' => $categories,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

    public function create()
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

        $validator = new Validator();
        if (
            !$validator->validate($data, [
                'title' => ['required', 'min:3'],
                'description' => ['max:1000'],
                'categorie_id' => ['required', 'integer'],
                'status' => ['in:actif,inactif'],
                'duration' => ['integer']
            ])
        ) {
            Session::setError("Données invalides.");
            $this->redirect('/teacher/quizzes/create');
        }

        $category = $this->categories->findById($data['categorie_id']);
        if (!$category || $category->enseignant_id !== Session::getUserId()) {
            Session::setError("Catégorie invalide.");
            $this->redirect('/teacher/quizzes/create');
        }

        $quiz = new Quiz($data);
        $quiz->enseignant_id = Session::getUserId();
        $quizId = $this->quizzes->create($quiz);

        $questions = $_POST['questions'] ?? [];
        if (empty($questions)) {
            Session::setError("Au moins une question est requise.");
            $this->quizzes->delete($quizId);
            $this->redirect('/teacher/quizzes/create');
        }

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

        Session::setSuccess("Quiz créé.");
        $this->redirect('/teacher/quizzes');
    }

    public function showEdit()
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');

        $id = (int) ($_GET['id'] ?? 0);
        $quiz = $this->quizzes->findById($id);
        if (!$quiz || $quiz->enseignant_id !== Session::getUserId()) {
            Session::setError("Quiz introuvable.");
            $this->redirect('/teacher/quizzes');
        }

        $categories = $this->categories->findByEnseignant(Session::getUserId());
        $questions = $this->questions->findByQuiz($id);

        $this->view('teacher/quiz/edit', [
            'quiz' => $quiz,
            'categories' => $categories,
            'questions' => $questions,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

    public function update()
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');
        CSRFMiddleware::handle();

        $id = (int) ($_POST['id'] ?? 0);
        $quiz = $this->quizzes->findById($id);
        if (!$quiz || $quiz->enseignant_id !== Session::getUserId()) {
            Session::setError("Quiz introuvable.");
            $this->redirect('/teacher/quizzes');
        }

        $data = [
            'title' => Security::sanitize($_POST['title'] ?? ''),
            'description' => Security::sanitize($_POST['description'] ?? ''),
            'categorie_id' => (int) ($_POST['categorie_id'] ?? 0),
            'status' => Security::sanitize($_POST['status'] ?? 'actif'),
            'duration' => (int) ($_POST['duration'] ?? 30),
        ];

        $validator = new Validator();
        if (
            !$validator->validate($data, [
                'title' => ['required', 'min:3'],
                'description' => ['max:1000'],
                'categorie_id' => ['required', 'integer'],
                'status' => ['in:actif,inactif'],
                'duration' => ['integer']
            ])
        ) {
            Session::setError("Données invalides.");
            $this->redirect("/teacher/quizzes/edit?id={$id}");
        }

        $category = $this->categories->findById($data['categorie_id']);
        if (!$category || $category->enseignant_id !== Session::getUserId()) {
            Session::setError("Catégorie invalide.");
            $this->redirect("/teacher/quizzes/edit?id={$id}");
        }

        $quiz->title = $data['title'];
        $quiz->description = $data['description'];
        $quiz->categorie_id = $data['categorie_id'];
        $quiz->status = $data['status'];
        $quiz->duration = $data['duration'];
        $this->quizzes->update($quiz);
        Session::setSuccess("Quiz mis à jour.");
        $this->redirect('/teacher/quizzes');
    }

    public function delete()
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');
        CSRFMiddleware::handle();

        $id = (int) ($_POST['id'] ?? 0);
        $quiz = $this->quizzes->findById($id);
        if (!$quiz || $quiz->enseignant_id !== Session::getUserId()) {
            Session::setError("Quiz introuvable.");
            $this->redirect('/teacher/quizzes');
        }

        $this->quizzes->delete($id);
        Session::setSuccess("Quiz supprimé.");
        $this->redirect('/teacher/quizzes');
    }
}

