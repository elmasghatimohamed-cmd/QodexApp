<?php

namespace App\Repositories;
use App\models\StudentAnswer;
use PDO;

class StudentAnswerRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM student_answers");
        $answers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $answers[] = new StudentAnswer($row);
        }
        return $answers;
    }
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM student_answers WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new StudentAnswer($row) : null;
    }
    public function findByStudentQuiz($studentQuizId)
    {
        $stmt = $this->db->prepare("SELECT * FROM student_answers WHERE student_quiz_id = :student_quiz_id");
        $stmt->execute(['student_quiz_id' => $studentQuizId]);
        $answers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $answers[] = new StudentAnswer($row);
        }
        return $answers;
    }
    public function findByStudentQuizAndQuestion($studentQuizId, $questionId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM student_answers 
             WHERE student_quiz_id = :student_quiz_id AND question_id = :question_id"
        );
        $stmt->execute([
            'student_quiz_id' => $studentQuizId,
            'question_id' => $questionId
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new StudentAnswer($row) : null;
    }
    public function countCorrectAnswersByStudentQuiz($studentQuizId)
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total FROM student_answers 
             WHERE student_quiz_id = :student_quiz_id AND is_correct = 1"
        );
        $stmt->execute(['student_quiz_id' => $studentQuizId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    public function calculateScoreByStudentQuiz($studentQuizId)
    {
        $stmt = $this->db->prepare(
            "SELECT SUM(q.points) as score 
             FROM student_answers sa
             JOIN questions q ON sa.question_id = q.id
             WHERE sa.student_quiz_id = :student_quiz_id AND sa.is_correct = 1"
        );
        $stmt->execute(['student_quiz_id' => $studentQuizId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['score'] ?? 0;
    }
    public function create(StudentAnswer $studentAnswer)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO student_answers (student_quiz_id, question_id, answer_id, reponse_texte, is_correct, created_at) 
             VALUES (:student_quiz_id, :question_id, :answer_id, :reponse_texte, :is_correct, NOW())"
        );

        $stmt->execute([
            'student_quiz_id' => $studentAnswer->student_quiz_id,
            'question_id' => $studentAnswer->question_id,
            'answer_id' => $studentAnswer->answer_id,
            'reponse_texte' => $studentAnswer->reponse_texte,
            'is_correct' => $studentAnswer->is_correct ? 1 : 0
        ]);

        return $this->db->lastInsertId();
    }
    public function update(StudentAnswer $studentAnswer)
    {
        $stmt = $this->db->prepare(
            "UPDATE student_answers SET 
                answer_id = :answer_id,
                reponse_texte = :reponse_texte,
                is_correct = :is_correct
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $studentAnswer->id,
            'answer_id' => $studentAnswer->answer_id,
            'reponse_texte' => $studentAnswer->reponse_texte,
            'is_correct' => $studentAnswer->is_correct ? 1 : 0
        ]);
    }
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM student_answers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    public function deleteByStudentQuiz($studentQuizId)
    {
        $stmt = $this->db->prepare("DELETE FROM student_answers WHERE student_quiz_id = :student_quiz_id");
        return $stmt->execute(['student_quiz_id' => $studentQuizId]);
    }
}