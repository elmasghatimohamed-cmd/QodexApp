<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\CSRFMiddleware;
use App\Middleware\RoleMiddleware;
use App\Models\StudentQuiz;
use App\Repositories\QuizRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\AnswerRepository;
use App\Repositories\StudentQuizRepository;
use App\Repositories\StudentAnswerRepository;

class StudentQuizController extends BaseController
{
    private QuizRepository $quizzes;
    private QuestionRepository $questions;
    private AnswerRepository $answers;
    private StudentQuizRepository $attempts;
    private StudentAnswerRepository $studentAnswers;

    public function __construct($db)
    {
        $this->quizzes = new QuizRepository($db);
        $this->questions = new QuestionRepository($db);
        $this->answers = new AnswerRepository($db);
        $this->attempts = new StudentQuizRepository($db);
        $this->studentAnswers = new StudentAnswerRepository($db);
    }

    public function listActive()
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('etudiant');
        $quizzes = $this->quizzes->findByStatus('actif');
        $this->view('student/quiz/list', [
            'quizzes' => $quizzes,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

    public function take()
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('etudiant');

        $quizId = (int) ($_GET['id'] ?? 0);
        $quiz = $this->quizzes->findById($quizId);
        if (!$quiz || $quiz->status !== 'actif') {
            Session::setError("Quiz non disponible.");
            $this->redirect('/student/quizzes');
        }

        $questions = $this->questions->findByQuiz($quizId);
        $allAnswers = $this->answers->findAnswersByQuiz($quizId);
        $answersByQuestion = [];
        foreach ($allAnswers as $answer) {
            if (!isset($answersByQuestion[$answer->question_id])) {
                $answersByQuestion[$answer->question_id] = [];
            }
            $answersByQuestion[$answer->question_id][] = $answer;
        }

        $this->view('student/quiz/take', [
            'quiz' => $quiz,
            'questions' => $questions,
            'answersByQuestion' => $answersByQuestion,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

    public function submit()
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('etudiant');
        CSRFMiddleware::handle();

        $quizId = (int) ($_POST['quiz_id'] ?? 0);
        $quiz = $this->quizzes->findById($quizId);
        if (!$quiz || $quiz->status !== 'actif') {
            Session::setError("Quiz non disponible.");
            $this->redirect('/student/quizzes');
        }

        $studentId = Session::getUserId();
        if ($this->attempts->findByQuizAndStudent($quizId, $studentId)) {
            Session::setError("Vous avez déjà soumis ce quiz.");
            $this->redirect('/student/quizzes');
        }

        $questions = $this->questions->findByQuiz($quizId);
        $answersInput = $_POST['answers'] ?? [];
        if (count($answersInput) < count($questions)) {
            Session::setError("Toutes les réponses sont requises.");
            $this->redirect("/student/quiz/take?id={$quizId}");
        }

        $totalPoints = $this->questions->getTotalPointsByQuiz($quizId);
        $score = 0;
        $attempt = new StudentQuiz([
            'quiz_id' => $quizId,
            'etudiant_id' => $studentId,
            'started_at' => date('Y-m-d H:i:s'),
        ]);
        $attemptId = $this->attempts->create($attempt);

        foreach ($questions as $question) {
            $provided = $answersInput[$question->id] ?? null;
            $isCorrect = false;
            $answerId = null;
            $reponseTexte = null;

            if ($question->type_question === 'reponse_courte') {
                $reponseTexte = Security::sanitize($provided);
                $isCorrect = false;
            } else {
                $answerId = (int) $provided;
                $correctAnswers = $this->answers->findCorrectAnswersByQuestion($question->id);
                foreach ($correctAnswers as $correct) {
                    if ($correct->id == $answerId) {
                        $isCorrect = true;
                        break;
                    }
                }
            }

            if ($isCorrect) {
                $score += (int) $question->points;
            }

            $this->studentAnswers->create(new \App\Models\StudentAnswer([
                'student_quiz_id' => $attemptId,
                'question_id' => $question->id,
                'answer_id' => $answerId,
                'reponse_texte' => $reponseTexte,
                'is_correct' => $isCorrect
            ]));
        }

        $attempt->id = $attemptId;
        $attempt->updateScore($score, $totalPoints);
        $attempt->completed_at = date('Y-m-d H:i:s');
        $attempt->temps_passe_minutes = (int) ($_POST['temps_passe_minutes'] ?? 0);
        $this->attempts->complete($attempt);

        Session::setSuccess("Quiz soumis. Score: {$score}/{$totalPoints}");
        $this->redirect('/student/results');
    }
}

