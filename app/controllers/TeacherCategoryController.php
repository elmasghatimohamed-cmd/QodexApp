<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Middleware\AuthMiddleware;
use App\Middleware\CSRFMiddleware;
use App\Middleware\RoleMiddleware;
use App\Models\Category;
use App\Repositories\CategoryRepository;

class TeacherCategoryController extends BaseController
{
    private CategoryRepository $categories;

    public function __construct($db)
    {
        $this->categories = new CategoryRepository($db);
    }

    public function index()
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');

        $enseignantId = Session::getUserId();
        $categories = $this->categories->findByEnseignant($enseignantId);

        $this->view('teacher/category/index', [
            'categories' => $categories,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

    public function showCreate()
    {

        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');

        $this->view('teacher/category/create', [
            'error' => Session::getError(),
            'success' => Session::getError()
        ]);
    }

    public function create()
    {

        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');
        CSRFMiddleware::handle();

        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'description' => Security::sanitize($_POST['description'] ?? '')
        ];

        $validator = new Validator();
        if (
            !$validator->validate($data, [
                'name' => ['required', 'min:3', 'max:100'],
                'description' => ['max:500']
            ])
        ) {
            Session::setError('Les données sont invalide');
            $this->redirect('/teacher/categories/create');
        }

        $category = new Category($data);
        $category->enseignat_id = Session::getUserId();
        $this->categories->create($category);
        Session::setSuccess('Catégorie créé avec success');
    }

    public function showEdit()
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');

        $id = (int) ($_GET['id'] ?? 0);
        $category = $this->categories->findById($id);
        if (!$category || $category->enseignant_id !== Session::getUserId()) {
            Session::setError("Catégorie introuvable.");
            $this->redirect('/teacher/categories');
        }

        $this->view('teacher/category/edit', [
            'category' => $category,
            'error' => Session::getError(),
            'success' => Session::getSuccess()
        ]);
    }

    public function update()
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');
        CSRFMiddleware::handle();

        $id = (int) ($_POST['id'] ?? 0);
        $category = $this->categories->findById($id);
        if (!$category || $category->enseignant_id !== Session::getUserId()) {
            Session::setError("Catégorie introuvable.");
            $this->redirect('/teacher/categories');
        }

        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'description' => Security::sanitize($_POST['description'] ?? '')
        ];

        $validator = new Validator();
        if (
            !$validator->validate($data, [
                'name' => ['required', 'min:3', 'max:100'],
                'description' => ['max:500']
            ])
        ) {
            Session::setError("Données invalides.");
            $this->redirect("/teacher/categories/edit?id={$id}");
        }

        $category->name = $data['name'];
        $category->description = $data['description'];
        $this->categories->update($category);
        Session::setSuccess("Catégorie mise à jour.");
        $this->redirect('/teacher/categories');
    }

    public function delete()
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle('enseignant');
        CSRFMiddleware::handle();

        $id = (int) ($_POST['id'] ?? 0);
        $category = $this->categories->findById($id);
        if (!$category || $category->enseignant_id !== Session::getUserId()) {
            Session::setError("Catégorie introuvable.");
            $this->redirect('/teacher/categories');
        }

        $this->categories->delete($id);
        Session::setSuccess("Catégorie supprimée.");
        $this->redirect('/teacher/categories');
    }
}

