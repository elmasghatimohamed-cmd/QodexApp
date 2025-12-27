<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Helpers\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Repositories\StudentQuizRepository;
use App\Repositories\QuizRepository;

class TeacherResultController extends BaseController
{
    private StudentQuizRepository $attempts;
    private QuizRepository $quizzes;

    public function __construct($db)
    {
        $this->attempts = new StudentQuizRepository($db);
        $this->quizzes = new QuizRepository($db);
    }

    public function show(int $quizId): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');

        $enseignantId = Session::getUserId();

        $quiz = $this->quizzes->findById($quizId);
        if (!$quiz || $quiz->enseignant_id !== $enseignantId) {
            Session::setError("Quiz non autorisé.");
            $this->redirect('/teacher/quizzes');
        }

        $attempts = $this->attempts->findByQuizForTeacher($quizId, $enseignantId, 20, 0);

        $this->view('teacher/quiz/results', [
            'attempts' => $attempts,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

}