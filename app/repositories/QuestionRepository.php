<?php

namespace App\Repositories;
use App\models\Question;
use PDO;

class QuestionRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM questions WHERE deleted_at IS NULL");
        $stmt->execute();
        $questions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $questions[] = new Question($row);
        }
        return $questions;
    }
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM questions WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Question($row) : null;
    }

    public function findByQuiz($quizId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM questions 
             WHERE quiz_id = :quiz_id AND deleted_at IS NULL 
             ORDER BY ordre ASC"
        );
        $stmt->execute(['quiz_id' => $quizId]);
        $questions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $questions[] = new Question($row);
        }
        return $questions;
    }
    public function findByQuizAndType($quizId, $type)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM questions 
             WHERE quiz_id = :quiz_id AND type_question = :type AND deleted_at IS NULL 
             ORDER BY ordre ASC"
        );
        $stmt->execute([
            'quiz_id' => $quizId,
            'type' => $type
        ]);
        $questions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $questions[] = new Question($row);
        }
        return $questions;
    }
    public function getTotalPointsByQuiz($quizId)
    {
        $stmt = $this->db->prepare(
            "SELECT SUM(points) as total FROM questions 
             WHERE quiz_id = :quiz_id AND deleted_at IS NULL"
        );
        $stmt->execute(['quiz_id' => $quizId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function create(Question $question)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO questions (quiz_id, text, type_question, points, ordre, created_at) 
             VALUES (:quiz_id, :text, :type_question, :points, :ordre, NOW())"
        );

        $stmt->execute([
            'quiz_id' => $question->quiz_id,
            'text' => $question->text,
            'type_question' => $question->type_question,
            'points' => $question->points,
            'ordre' => $question->ordre
        ]);

        return $this->db->lastInsertId();
    }

    public function update(Question $question)
    {
        $stmt = $this->db->prepare(
            "UPDATE questions SET 
                text = :text,
                type_question = :type_question,
                points = :points,
                ordre = :ordre
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $question->id,
            'text' => $question->text,
            'type_question' => $question->type_question,
            'points' => $question->points,
            'ordre' => $question->ordre
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("UPDATE questions SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}