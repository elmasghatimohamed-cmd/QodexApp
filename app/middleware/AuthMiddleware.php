<?php

namespace App\Middleware;

use App\Helpers\Session;

class AuthMiddleware
{
    public static function handle(): bool
    {
        Session::start();

        if (!Session::isLoggedIn()) {
            Session::setError("Vous devez être connecté pour accéder à cette page.");
            header('Location: /login');
            exit;
        }
        return true;
    }

    public static function guest(): bool
    {
        Session::start();

        if (Session::isLoggedIn()) {
            $user = Session::get('user');
            $role = $user['role'] ?? null;

            if ($role == 'enseignant') {
                header('Location: /teacher/dashboard');
            } else {
                header('Location: /student/dashboard');
            }
            exit;
        }

        return true;
    }
}