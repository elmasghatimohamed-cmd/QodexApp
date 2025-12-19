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

    public function index()
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('etudiant');

        $attempts = $this->attempts->findByEtudiant(Session::getUserId(), 50, 0);
        $this->view('student/results/index', [
            'attempts' => $attempts,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }
}

