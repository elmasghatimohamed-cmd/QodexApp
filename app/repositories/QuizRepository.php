<?php

namespace App\Repositories;
use App\Models\Quiz;
use PDO;

class QuizRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE deleted_at IS NULL");
        $stmt->execute();
        $quizzes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $quizzes[] = new Quiz($row);
        }
        return $quizzes;
    }
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Quiz($row) : null;
    }
    public function findByEnseignant($enseignantId)
    {
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE enseignant_id = :enseignant_id AND deleted_at IS NULL");
        $stmt->execute(['enseignant_id' => $enseignantId]);
        $quizzes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $quizzes[] = new Quiz($row);
        }
        return $quizzes;
    }

    public function findByCategory($categoryId)
    {
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE categorie_id = :categorie_id AND deleted_at IS NULL");
        $stmt->execute(['categorie_id' => $categoryId]);
        $quizzes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $quizzes[] = new Quiz($row);
        }
        return $quizzes;
    }
    public function findByStatus($status)
    {
        $stmt = $this->db->prepare("SELECT * FROM quizzes WHERE status = :status AND deleted_at IS NULL");
        $stmt->execute(['status' => $status]);
        $quizzes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $quizzes[] = new Quiz($row);
        }
        return $quizzes;
    }
    public function findActiveByCategoryId($categoryId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM quizzes 
             WHERE categorie_id = :categorie_id 
             AND status = 'actif' 
             AND deleted_at IS NULL"
        );
        $stmt->execute(['categorie_id' => $categoryId]);
        $quizzes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $quizzes[] = new Quiz($row);
        }
        return $quizzes;
    }
    public function create(Quiz $quiz)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO quizzes (title, description, categorie_id, enseignant_id, status, duration, created_at) 
             VALUES (:title, :description, :categorie_id, :enseignant_id, :status, :duration, NOW())"
        );

        $stmt->execute([
            'title' => $quiz->title,
            'description' => $quiz->description,
            'categorie_id' => $quiz->categorie_id,
            'enseignant_id' => $quiz->enseignant_id,
            'status' => $quiz->status,
            'duration' => $quiz->duration
        ]);

        return $this->db->lastInsertId();
    }
    public function update(Quiz $quiz)
    {
        $stmt = $this->db->prepare(
            "UPDATE quizzes SET 
                title = :title,
                description = :description,
                categorie_id = :categorie_id,
                status = :status,
                duration = :duration,
                updated_at = NOW()
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $quiz->id,
            'title' => $quiz->title,
            'description' => $quiz->description,
            'categorie_id' => $quiz->categorie_id,
            'status' => $quiz->status,
            'duration' => $quiz->duration
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("UPDATE quizzes SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}