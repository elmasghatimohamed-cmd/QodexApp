<?php

namespace App\Middleware;
use App\Helpers\Session;
use App\Helpers\Security;

class CSRFMiddleware
{

    public static function handle()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            return True;
        }

        Session::start();

        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!$token || !Security::verifyCSRFToken($token)) {
            Session::setError("Token CSRF invalide. Veuillez réessayer.");
            if (isset($GLOBALS['db'])) {
                Security::logSecurityEvent(
                    $GLOBALS['db'],
                    Session::getUserId(),
                    'csrf_validation_failed',
                    'Token CSRF invalide pour ' . $_SERVER['REQUEST_URI']
                );
            }
            http_response_code(403);
            die("Erreur de sécurité: Token CSRF invalide.");
        }
        return true;
    }

    public static function getToken()
    {
        return Security::generateCSRFToken();
    }

}