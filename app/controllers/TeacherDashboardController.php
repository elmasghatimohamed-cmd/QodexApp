<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Helpers\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Repositories\QuizRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\StudentQuizRepository;

class TeacherDashboardController extends BaseController
{
    private QuizRepository $quizzes;
    private CategoryRepository $categories;
    private StudentQuizRepository $attempts;

    public function __construct($db)
    {
        $this->quizzes = new QuizRepository($db);
        $this->categories = new CategoryRepository($db);
        $this->attempts = new StudentQuizRepository($db);
    }

    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');

        $enseignantId = Session::getUserId();

        $allQuizzes = $this->quizzes->findByEnseignant($enseignantId);
        $allCategories = $this->categories->findByEnseignant($enseignantId);

        $totalQuizzes = count($allQuizzes);
        $totalCategories = count($allCategories);

        $activeQuizzes = array_filter($allQuizzes, function ($quiz) {
            return $quiz->status === 'actif';
        });

        $recentQuizzes = array_slice($allQuizzes, 0, 5);

        $totalAttempts = 0;
        foreach ($allQuizzes as $quiz) {
            $quizAttempts = $this->attempts->findByQuizForTeacher($quiz->id, $enseignantId, 1000, 0);
            $totalAttempts += count($quizAttempts);
        }

        $this->view('teacher/dashboard', [
            'totalQuizzes' => $totalQuizzes,
            'totalCategories' => $totalCategories,
            'activeQuizzes' => count($activeQuizzes),
            'totalAttempts' => $totalAttempts,
            'recentQuizzes' => $recentQuizzes,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }
}