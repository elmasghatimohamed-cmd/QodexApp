<?php

namespace App\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Middleware\AuthMiddleware;
use App\Core\BaseController;

class AuthController extends BaseController
{
    private UserRepository $userRepository;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->userRepository = new UserRepository($this->db);
    }


    public function showLoginForm(): void
    {
        AuthMiddleware::guest();
        $this->view('auth/login', [
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

    public function login(): void
    {
        AuthMiddleware::guest();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? null)) {
            Session::setError("Erreur de sécurité : token CSRF invalide.");
            $this->redirect('/login');
        }

        $email = Security::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $validator = new Validator();
        if (
            !$validator->validate(
                ['email' => $email, 'password' => $password],
                ['email' => ['required', 'email'], 'password' => ['required']]
            )
        ) {
            Session::setError("Email ou mot de passe invalide.");
            $this->redirect('/login');
        }

        if (Security::checkBruteForce($email)) {
            Session::setError("Trop de tentatives. Réessayez plus tard.");
            $this->redirect('/login');
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Security::verifyPassword($password, $user->password)) {
            Security::recordFailedLogin($email);
            Session::setError("Email ou mot de passe incorrect.");
            $this->redirect('/login');
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

        $this->redirect(
            $user->role === 'enseignant'
            ? '/teacher/dashboard'
            : '/student/dashboard'
        );
    }


    public function showRegisterForm(): void
    {
        AuthMiddleware::guest();
        $this->view('auth/register', [
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

    public function register(): void
    {
        AuthMiddleware::guest();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }

        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? null)) {
            Session::setError("Erreur de sécurité : token CSRF invalide.");
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

        $this->userRepository->create($user);

        Session::setSuccess("Compte créé avec succès. Connectez-vous.");
        $this->redirect('/login');
    }


    public function logout(): void
    {
        AuthMiddleware::handle();
        Session::destroy();
        $this->redirect('/login');
    }
}
