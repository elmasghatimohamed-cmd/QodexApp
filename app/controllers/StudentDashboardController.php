<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Helpers\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Repositories\QuizRepository;
use App\Repositories\StudentQuizRepository;

class StudentDashboardController extends BaseController
{
    private QuizRepository $quizzes;
    private StudentQuizRepository $attempts;

    public function __construct($db)
    {
        $this->quizzes = new QuizRepository($db);
        $this->attempts = new StudentQuizRepository($db);
    }

    public function index()
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('etudiant');

        $studentId = Session::getUserId();

        $availableQuizzes = $this->quizzes->findByStatus('actif');

        $myAttempts = $this->attempts->findByEtudiant($studentId, 1000, 0);
        $totalAttempts = count($myAttempts);

        $recentAttempts = array_slice($myAttempts, 0, 5);

        $averageScore = 0;
        if ($totalAttempts > 0) {
            $totalPercentage = 0;
            foreach ($myAttempts as $attempt) {
                $totalPercentage += $attempt->pourcentage;
            }
            $averageScore = round($totalPercentage / $totalAttempts, 2);
        }

        $this->view('student/dashboard', [
            'availableQuizzes' => count($availableQuizzes),
            'totalAttempts' => $totalAttempts,
            'averageScore' => $averageScore,
            'recentAttempts' => $recentAttempts,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }
}