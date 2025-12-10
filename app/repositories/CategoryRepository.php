<?php

namespace App\Repositories;

use App\models\Category;
use PDO;

class CategoryRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM categories WHERE deleted_at IS NULL");
        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category($row);
        }
        return $categories;
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Category($row) : null;
    }

    public function findByEnseignant($enseignantId)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE enseignant_id = :enseignant_id AND deleted_at IS NULL");
        $stmt->execute(['enseignant_id' => $enseignantId]);
        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category($row);
        }
        return $categories;
    }

    public function create(Category $category)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO categories (name, description, enseignant_id, created_at) 
             VALUES (:name, :description, :enseignant_id, NOW())"
        );

        $stmt->execute([
            'name' => $category->name,
            'description' => $category->description,
            'enseignant_id' => $category->enseignant_id
        ]);

        return $this->db->lastInsertId();
    }

    public function update(Category $category)
    {
        $stmt = $this->db->prepare(
            "UPDATE categories SET 
                name = :name,
                description = :description,
                updated_at = NOW()
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description
        ]);
    }
    public function delete($id)
    {
        $stmt = $this->db->prepare("UPDATE categories SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}