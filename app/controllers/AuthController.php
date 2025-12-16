<?php

namespace App\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Middleware\AuthMiddleware;
use App\Middleware\CSRFMiddleware;
use App\Core\BaseController;

class AuthController extends BaseController
{
    private $userRepository;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->userRepository = new UserRepository($this->db);
    }

    public function showLoginForm()
    {
        AuthMiddleware::guest();
        $this->view('auth/login', [
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

    public function login()
    {
        AuthMiddleware::guest();
        CSRFMiddleware::handle();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $email = Security::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $validator = new Validator();
        if (
            !$validator->validate(
                [
                    'email' => $email,
                    'password' => $password

                ],
                [
                    'email' => ['required', 'email'],
                    'password' => ['required']
                ]
            )
        ) {
            Session::setError("Email ou mot de passe invalide.");
            header('Location: /login');
            exit;
        }

        if (Security::checkBruteForce($email)) {
            Security::logSecurityEvent($this->db, null, 'brute_force_blocked', "Tentative de connexion bloquée pour: {$email}");
            Session::setError("Trop de tentatives de connexion. Veuillez réessayer dans 15 minutes.");
            header('Location: /login');
            exit;
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Security::verifyPassword($password, $user->password)) {
            Security::recordFailedLogin($email);
            Security::logSecurityEvent($this->db, null, 'login_failed', "Tentative de connexion échouée pour: {$email}");
            Session::setError("Email ou mot de passe incorrect.");
            header('Location: /login');
            exit;
        }

        Security::resetLoginAttempts($email);
        Session::regenerate();
        Session::setUser([
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
        ]);
        $this->userRepository->updateLastLogin($user->id);
        Security::logSecurityEvent($this->db, $user->id, 'login_success', "Connexion réussie pour: {$email}");

        if ($user->role === 'enseignant') {
            $this->redirect('/teacher/dashboard');
        }
        $this->redirect('/student/dashboard');
    }

    public function showRegisterForm()
    {
        AuthMiddleware::guest();
        $this->view('auth/register', [
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

    public function register()
    {
        AuthMiddleware::guest();
        CSRFMiddleware::handle();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }

        $data = [
            'email' => Security::sanitize($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
            'first_name' => Security::sanitize($_POST['first_name'] ?? ''),
            'last_name' => Security::sanitize($_POST['last_name'] ?? ''),
            'role' => Security::sanitize($_POST['role'] ?? 'etudiant'),
        ];

        $validator = new Validator();
        if (
            !$validator->validate(
                $data,
                [
                    'email' => ['required', 'email'],
                    'password' => ['required', 'min:8', 'confirmed'],
                    'first_name' => ['required', 'min:2'],
                    'last_name' => ['required', 'min:2'],
                    'role' => ['required', 'in:enseignant,etudiant']
                ]
            )
        ) {
            Session::setError("Données invalides.");
            $this->redirect('/register');
        }

        if ($this->userRepository->findByEmail($data['email'])) {
            Session::setError("Email déjà utilisé.");
            $this->redirect('/register');
        }

        $passwordErrors = Security::validatePasswordStrength($data['password']);
        if (!empty($passwordErrors)) {
            Session::setError(implode(', ', $passwordErrors));
            $this->redirect('/register');
        }

        $user = new User(
            $data['email'],
            Security::hashPassword($data['password']),
            $data['first_name'],
            $data['last_name'],
            $data['role']
        );

        $userId = $this->userRepository->create($user);
        Security::logSecurityEvent($this->db, $userId, 'register', "Création de compte {$data['email']}");
        Session::setSuccess("Compte créé, vous pouvez vous connecter.");
        $this->redirect('/login');
    }

    public function logout()
    {
        AuthMiddleware::handle();
        $userId = Session::getUserId();
        Session::destroy();
        Security::logSecurityEvent($this->db, $userId, 'logout', 'Déconnexion utilisateur');
        $this->redirect('/login');
    }

}