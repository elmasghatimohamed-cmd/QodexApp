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

// Auth
$router->post('/login', fn() => new AuthController($db))->login();
$router->get('/register', fn() => new AuthController($db))->showRegisterForm();
$router->post('/register', fn() => new AuthController($db))->register();
$router->post('/login', fn() => new AuthController($db))->login();

// Teacher categories
$router->get('/teacher/categories', fn() => new TeacherCategoryController($db))->index();
$router->get('/teacher/categories/create', fn() => new TeacherCategoryController($db))->showCreate();
$router->post('/teacher/categories/create', fn() => new TeacherCategoryController($db))->create();
$router->get('/teacher/categories/edit', fn() => new TeacherCategoryController($db))->showEdit();
$router->post('/teacher/categories/edit', fn() => new TeacherCategoryController($db))->update();
$router->post('/teacher/categories/delete', fn() => new TeacherCategoryController($db))->delete();

// Teacher quizzes
$router->get('/teacher/dashboard', fn() => new TeacherDashboardController($db))->index();
$router->get('/teacher/quizzes', fn() => new TeacherQuizController($db))->index();
$router->get('/teacher/quizzes/create', fn() => new TeacherQuizController($db))->showCreate();
$router->post('/teacher/quizzes/create', fn() => new TeacherQuizController($db))->create();
$router->get('/teacher/quizzes/edit', fn() => new TeacherQuizController($db))->showEdit();
$router->post('/teacher/quizzes/edit', fn() => new TeacherQuizController($db))->update();
$router->post('/teacher/quizzes/delete', fn() => new TeacherQuizController($db))->delete();
$router->get('/teacher/quizzes/results', fn() => new TeacherResultController($db))->index();

// Student
$router->get('/student/dashboard', fn() => new StudentDashboardController($db))->index();
$router->get('/student/quizzes', fn() => new StudentQuizController($db))->listActive();
$router->get('/student/quiz/take', fn() => new StudentQuizController($db))->take();
$router->post('/student/quiz/submit', fn() => new StudentQuizController($db))->submit();
$router->get('/student/results', fn() => new StudentResultController($db))->index();

// Default
$router->get('/', fn() => header('Location: /login'));

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

