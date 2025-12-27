<?php

namespace App\Repositories;

use App\Models\Answer;
use PDO;

class AnswerRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM answers WHERE deleted_at IS NULL");
        $stmt->execute();
        $answers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $answers[] = new Answer($row);
        }
        return $answers;
    }

    public function findById($id): ?Answer
    {
        $stmt = $this->db->prepare("SELECT * FROM answers WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Answer($row) : null;
    }

    public function findByQuestion($questionId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM answers WHERE question_id = :question_id AND deleted_at IS NULL");
        $stmt->execute(['question_id' => $questionId]);
        $answers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $answers[] = new Answer($row);
        }
        return $answers;
    }

    public function findCorrectAnswersByQuestion($questionId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM answers 
             WHERE question_id = :question_id AND is_correct = 1 AND deleted_at IS NULL"
        );
        $stmt->execute(['question_id' => $questionId]);
        $answers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $answers[] = new Answer($row);
        }
        return $answers;
    }

    public function findAnswersByQuiz(int $quizId): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.* FROM answers a 
             JOIN questions q ON a.question_id = q.id 
             WHERE q.quiz_id = :quiz_id AND a.deleted_at IS NULL"
        );
        $stmt->execute(['quiz_id' => $quizId]);
        $answers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $answers[] = new Answer($row);
        }
        return $answers;
    }

    public function create(Answer $answer): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO answers (question_id, text, is_correct, created_at) 
             VALUES (:question_id, :text, :is_correct, NOW())"
        );

        $stmt->execute([
            'question_id' => $answer->question_id,
            'text' => $answer->text,
            'is_correct' => $answer->is_correct ? 1 : 0
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(Answer $answer): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE answers SET 
                text = :text,
                is_correct = :is_correct
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $answer->id,
            'text' => $answer->text,
            'is_correct' => $answer->is_correct ? 1 : 0
        ]);
    }

    public function delete($id): bool
    {
        $stmt = $this->db->prepare("UPDATE answers SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}