<?php

namespace App\Helpers;

use PDOException;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Strict');

            session_start();

            if (!isset($_SESSION['created'])) {
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            } elseif (time() - $_SESSION['created'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function set($key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove($key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function getUser(): ?array
    {
        return self::get('user');
    }

    public static function setUser($user): void
    {
        self::set('user', $user);
    }

    public static function isLoggedIn(): bool
    {
        return self::has('user') && self::get('user') !== null;
    }

    public static function hasRole($role): bool
    {
        $user = self::getUser();
        return $user && isset($user['role']) && $user['role'] === $role;
    }

    public static function getUserId(): ?int
    {
        $user = self::getUser();
        return $user['id'] ?? null;
    }

    public static function setFlash($key, $message): void
    {
        self::start();
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        $_SESSION['flash'][$key] = $message;
    }

    public static function getFlash($key, $default = null)
    {
        self::start();
        $message = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    public static function setError($message): void
    {
        self::setFlash('error', $message);
    }

    public static function setSuccess($message): void
    {
        self::setFlash('success', $message);
    }

    public static function getError()
    {
        return self::getFlash('error');
    }

    public static function getSuccess()
    {
        return self::getFlash('success');
    }

    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();
    }

    public static function cleanupExpiredSessions($db): void
    {
        try {
            $stmt = $db->prepare(
                "DELETE FROM sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 24 HOUR)"
            );
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors du nettoyage des sessions: " . $e->getMessage());
        }
    }
}