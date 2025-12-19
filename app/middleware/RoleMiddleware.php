<?php

namespace App\Middleware;
use App\Helpers\Session;

class RoleMiddleware
{

    public static function handle($requiredRole)
    {
        Session::start();

        if (!Session::isLoggedIn()) {
            Session::setError("Vous devez etre connecté d'abord pour accéder à cette page.");
            header("Location: /login");
            exit;
        }
        $user = Session::getUser();
        $userRole = $user['role'] ?? null;

        if ($userRole !== $requiredRole) {
            Session::setError("Accès Refusé. Vous n'avez pas les permission nécessaires pour accéder à cette page.");
            if ($userRole == 'enseignant') {
                header("Location: /teacher/dashboard");
            }
            if ($userRole == "etudiant") {
                header("Location: /student/dashboard");
            }
            exit;
        }
        return true;
    }

    public function requireTeacher()
    {
        return self::handle("enseignant");
    }

    public function requireStudent()
    {
        return self::handle("etudiant");
    }
}