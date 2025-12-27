<?php

use App\Controllers\TeacherDashboardController;
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\TeacherCategoryController;
use App\Controllers\TeacherQuizController;
use App\Controllers\TeacherResultController;
use App\Controllers\StudentQuizController;
use App\Controllers\StudentResultController;
use App\Controllers\StudentDashboardController;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();
$GLOBALS['db'] = $db;

$router = new Router();

// ==================== Routes d'authentification ====================
$router->get('/login', fn() => (new AuthController($db))->showLoginForm());
$router->post('/login', fn() => (new AuthController($db))->login());
$router->get('/register', fn() => (new AuthController($db))->showRegisterForm());
$router->post('/register', fn() => (new AuthController($db))->register());
$router->post('/logout', fn() => (new AuthController($db))->logout());

// ==================== ENSEIGNANT ====================

// Tableau de bord enseignant
$router->get('/teacher/dashboard', fn() => (new TeacherDashboardController($db))->index());

// Gestion des catégories (NOTATION :param)
$router->get('/teacher/categories', fn() => (new TeacherCategoryController($db))->index());
$router->get('/teacher/categories/create', fn() => (new TeacherCategoryController($db))->showCreate());
$router->post('/teacher/categories', fn() => (new TeacherCategoryController($db))->create());
$router->get('/teacher/categories/edit/:id', fn($id) => (new TeacherCategoryController($db))->showEdit());
$router->post('/teacher/categories/update/:id', fn($id) => (new TeacherCategoryController($db))->update());
$router->post('/teacher/categories/delete/:id', fn($id) => (new TeacherCategoryController($db))->delete());

// Gestion des quizzes (NOTATION :param)
$router->get('/teacher/quizzes', fn() => (new TeacherQuizController($db))->index());
$router->get('/teacher/quizzes/create', fn() => (new TeacherQuizController($db))->showCreate());
$router->post('/teacher/quizzes', fn() => (new TeacherQuizController($db))->create());
$router->get('/teacher/quizzes/edit/:id', fn($id) => (new TeacherQuizController($db))->showEdit($id));
$router->post('/teacher/quizzes/update/:id', fn($id) => (new TeacherQuizController($db))->update($id));
$router->post('/teacher/quizzes/delete/:id', fn($id) => (new TeacherQuizController($db))->delete($id));
$router->get('/teacher/quizzes/results/:id', fn($id) => (new TeacherResultController($db))->show($id));

// ==================== ÉTUDIANT ====================

// Tableau de bord étudiant
$router->get('/student/dashboard', fn() => (new StudentDashboardController($db))->index());

// Quiz étudiants
$router->get('/student/quizzes', fn() => (new StudentQuizController($db))->listActive());
$router->get('/student/quiz/take', fn() => (new StudentQuizController($db))->take());
$router->post('/student/quiz/submit', fn() => (new StudentQuizController($db))->submit());

// Résultats étudiants
$router->get('/student/results', fn() => (new StudentResultController($db))->index());

// ==================== Route par défaut ====================
$router->get('/', fn() => header('Location: /login'));

// ==================== Dispatch ====================
$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));