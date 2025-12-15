<?php

namespace App\Helpers;

use PDOException;
class Security
{
    public static function generateCSRFToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (
            !isset($_SESSION['csrf_token']) ||
            isset($_SESSION['csrf_token_time']) && time() - $_SESSION['csrf_token_time'] > 3600
        ) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }

        return $_SESSION['csrf_token'];
    }
    public static function verifyCSRFToken($token)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        if (isset($_SESSION['csrf_token_time']) && time() - $_SESSION['csrf_token_time'] > 3600) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }
    public static function escape($data, $flags = ENT_QUOTES, $encoding = 'UTF-8')
    {
        if (is_array($data)) {
            return array_map(function ($item) use ($flags, $encoding) {
                return self::escape($item, $flags, $encoding);
            }, $data);
        }
        return htmlspecialchars($data, $flags, $encoding);
    }
    public static function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return trim(strip_tags($data));
    }
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
    public static function validatePasswordStrength($password)
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une minuscule";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre";
        }

        return $errors;
    }
    public static function checkBruteForce($identifier, $maxAttempts = 5, $timeWindow = 900)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $key = 'login_attempts_' . md5($identifier);

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 0,
                'first_attempt' => time()
            ];
        }

        $attempts = $_SESSION[$key];
        if (time() - $attempts['first_attempt'] > $timeWindow) {
            $_SESSION[$key] = [
                'count' => 0,
                'first_attempt' => time()
            ];
            return false;
        }

        if ($attempts['count'] >= $maxAttempts) {
            return true;
        }

        return false;
    }
    public static function recordFailedLogin($identifier)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $key = 'login_attempts_' . md5($identifier);

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 0,
                'first_attempt' => time()
            ];
        }

        $_SESSION[$key]['count']++;
    }
    public static function resetLoginAttempts($identifier)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $key = 'login_attempts_' . md5($identifier);
        unset($_SESSION[$key]);
    }
    public static function logSecurityEvent($db, $userId, $action, $details = '')
    {
        try {
            $stmt = $db->prepare(
                "INSERT INTO security_logs (user_id, action, ip_address, user_agent, details, created_at) 
                 VALUES (:user_id, :action, :ip_address, :user_agent, :details, NOW())"
            );

            $stmt->execute([
                'user_id' => $userId,
                'action' => $action,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'details' => $details
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de l'enregistrement du log de sécurité: " . $e->getMessage());
        }
    }
    public static function setSecurityHeaders()
    {
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\';');
    }
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }
}

