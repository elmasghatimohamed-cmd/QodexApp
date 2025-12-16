<?php

namespace App\Middleware;
use App\Helpers\Session;

class AuthMiddleware
{

    public static function handle()
    {
        Session::start();

        if (!Session::isLoggedIn()) {
            Session::setError("Vous devez etre connecté pour accéder à cette page.");
            header('Location: /login', true);
            exit;
        }
        return True;
    }

    public static function guest()
    {
        Session::start();

        if (Session::isLoggedIn()) {
            $user = Session::get('user');
            $role = Session::get('role');

            if ($role == 'enseignant') {
                header('Location: /teacher/dashboard');
            } else {
                header('Location: /student/dashboard');
            }
            exit;
        }

        return True;

    }
}
