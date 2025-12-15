<?php
namespace App\Repositories;
use App\models\Answer;
use PDO;
class AnswerRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM answers WHERE deleted_at IS NULL");
        $stmt->execute();
        $answers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $answers[] = new Answer($row);
        }
        return $answers;
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM answers WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Answer($row) : null;
    }
    public function findByQuestion($questionId)
    {
        $stmt = $this->db->prepare("SELECT * FROM answers WHERE question_id = :question_id AND deleted_at IS NULL");
        $stmt->execute(['question_id' => $questionId]);
        $answers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $answers[] = new Answer($row);
        }
        return $answers;
    }
    public function findCorrectAnswersByQuestion($questionId)
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
    public function create(Answer $answer)
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

        return $this->db->lastInsertId();
    }
    public function update(Answer $answer)
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

    public function delete($id)
    {
        $stmt = $this->db->prepare("UPDATE answers SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}