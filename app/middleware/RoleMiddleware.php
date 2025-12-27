<?php

namespace App\Middleware;

use App\Helpers\Session;

class RoleMiddleware
{
    public static function handle($requiredRole): bool
    {
        Session::start();

        if (!Session::isLoggedIn()) {
            Session::setError("Vous devez être connecté d'abord pour accéder à cette page.");
            header("Location: /login");
            exit;
        }

        $user = Session::getUser();
        $userRole = $user['role'] ?? null;

        if ($userRole !== $requiredRole) {
            Session::setError("Accès Refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.");
            if ($userRole == 'enseignant') {
                header("Location: /teacher/dashboard");
            } elseif ($userRole == "etudiant") {
                header("Location: /student/dashboard");
            }
            exit;
        }
        return true;
    }

    public function requireTeacher(): bool
    {
        return self::handle("enseignant");
    }

    public function requireStudent(): bool
    {
        return self::handle("etudiant");
    }
}