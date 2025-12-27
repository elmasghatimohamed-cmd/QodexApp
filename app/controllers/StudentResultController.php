<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Helpers\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Repositories\StudentQuizRepository;

class StudentResultController extends BaseController
{
    private StudentQuizRepository $attempts;

    public function __construct($db)
    {
        $this->attempts = new StudentQuizRepository($db);
    }

    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('etudiant');

        $studentId = Session::getUserId();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $total = $this->attempts->countForStudent($studentId);
        $totalPages = (int) ceil($total / $limit);

        $attempts = $this->attempts->findForStudentWithQuiz($studentId, $limit, $offset);

        $this->view('student/results/index', [
            'attempts' => $attempts,
            'page' => $page,
            'totalPages' => $totalPages,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }
}