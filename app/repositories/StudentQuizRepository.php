<?php

namespace App\Repositories;

use App\Models\StudentQuiz;
use PDO;

class StudentQuizRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Résultats enseignant (tentatives) avec détails quiz + étudiant.
     * Retourne des arrays (DTO) pour éviter de mixer des modèles avec des JOIN.
     */
    public function findForTeacherWithDetails(int $enseignantId, ?int $quizId, int $limit, int $offset): array
    {
        $sql =
            "SELECT
                sq.id AS attempt_id,
                sq.quiz_id,
                q.title AS quiz_title,
                sq.etudiant_id,
                u.email AS student_email,
                u.first_name AS student_first_name,
                u.last_name AS student_last_name,
                sq.score,
                sq.total_points,
                sq.pourcentage,
                sq.temps_passe_minutes,
                sq.completed_at
             FROM student_quizzes sq
             JOIN quizzes q ON q.id = sq.quiz_id
             JOIN users u ON u.id = sq.etudiant_id
             WHERE q.enseignant_id = :enseignant_id
               AND q.deleted_at IS NULL";

        if ($quizId) {
            $sql .= " AND sq.quiz_id = :quiz_id";
        }

        $sql .= " ORDER BY sq.completed_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':enseignant_id', $enseignantId, PDO::PARAM_INT);
        if ($quizId) {
            $stmt->bindValue(':quiz_id', $quizId, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countForTeacher(int $enseignantId, ?int $quizId): int
    {
        $sql =
            "SELECT COUNT(*) AS total
             FROM student_quizzes sq
             JOIN quizzes q ON q.id = sq.quiz_id
             WHERE q.enseignant_id = :enseignant_id
               AND q.deleted_at IS NULL";
        $params = ['enseignant_id' => $enseignantId];
        if ($quizId) {
            $sql .= " AND sq.quiz_id = :quiz_id";
            $params['quiz_id'] = $quizId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Résultats étudiant avec titre du quiz.
     */
    public function findForStudentWithQuiz(int $etudiantId, int $limit, int $offset): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                sq.id AS attempt_id,
                sq.quiz_id,
                q.title AS quiz_title,
                sq.score,
                sq.total_points,
                sq.pourcentage,
                sq.temps_passe_minutes,
                sq.completed_at
             FROM student_quizzes sq
             JOIN quizzes q ON q.id = sq.quiz_id
             WHERE sq.etudiant_id = :etudiant_id
               AND q.deleted_at IS NULL
             ORDER BY sq.completed_at DESC
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':etudiant_id', $etudiantId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countForStudent(int $etudiantId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total
             FROM student_quizzes
             WHERE etudiant_id = :etudiant_id"
        );
        $stmt->execute(['etudiant_id' => $etudiantId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    public function averagePourcentageForStudent(int $etudiantId): float
    {
        $stmt = $this->db->prepare(
            "SELECT AVG(pourcentage) AS avg_p
             FROM student_quizzes
             WHERE etudiant_id = :etudiant_id"
        );
        $stmt->execute(['etudiant_id' => $etudiantId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($row['avg_p'] ?? 0);
    }

    public function findRecentForStudentWithQuiz(int $etudiantId, int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                sq.id AS attempt_id,
                sq.quiz_id,
                q.title AS quiz_title,
                sq.score,
                sq.total_points,
                sq.pourcentage,
                sq.temps_passe_minutes,
                sq.completed_at
             FROM student_quizzes sq
             JOIN quizzes q ON q.id = sq.quiz_id
             WHERE sq.etudiant_id = :etudiant_id
               AND q.deleted_at IS NULL
             ORDER BY sq.completed_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':etudiant_id', $etudiantId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?StudentQuiz
    {
        $stmt = $this->db->prepare("SELECT * FROM student_quizzes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new StudentQuiz($row) : null;
    }

    public function findByEtudiant(int $etudiantId, int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM student_quizzes WHERE etudiant_id = :etudiant_id ORDER BY completed_at DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':etudiant_id', $etudiantId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new StudentQuiz($row);
        }
        return $results;
    }

    public function findByQuizForTeacher(int $quizId, int $enseignantId, int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            "SELECT sq.* FROM student_quizzes sq
             JOIN quizzes q ON q.id = sq.quiz_id
             WHERE sq.quiz_id = :quiz_id AND q.enseignant_id = :enseignant_id
             ORDER BY sq.completed_at DESC
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':quiz_id', $quizId, PDO::PARAM_INT);
        $stmt->bindValue(':enseignant_id', $enseignantId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new StudentQuiz($row);
        }
        return $results;
    }

    public function create(StudentQuiz $attempt): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO student_quizzes (quiz_id, etudiant_id, score, total_points, pourcentage, temps_passe_minutes, started_at, completed_at)
             VALUES (:quiz_id, :etudiant_id, :score, :total_points, :pourcentage, :temps_passe_minutes, :started_at, :completed_at)"
        );
        $stmt->execute([
            'quiz_id' => $attempt->quiz_id,
            'etudiant_id' => $attempt->etudiant_id,
            'score' => $attempt->score,
            'total_points' => $attempt->total_points,
            'pourcentage' => $attempt->pourcentage,
            'temps_passe_minutes' => $attempt->temps_passe_minutes,
            'started_at' => $attempt->started_at ?? date('Y-m-d H:i:s'),
            'completed_at' => $attempt->completed_at,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findByQuizAndStudent(int $quizId, int $etudiantId): ?StudentQuiz
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM student_quizzes WHERE quiz_id = :quiz_id AND etudiant_id = :etudiant_id LIMIT 1"
        );
        $stmt->execute([
            'quiz_id' => $quizId,
            'etudiant_id' => $etudiantId
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new StudentQuiz($row) : null;
    }

    public function complete(StudentQuiz $attempt): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE student_quizzes SET
                score = :score,
                total_points = :total_points,
                pourcentage = :pourcentage,
                temps_passe_minutes = :temps_passe_minutes,
                completed_at = :completed_at
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $attempt->id,
            'score' => $attempt->score,
            'total_points' => $attempt->total_points,
            'pourcentage' => $attempt->pourcentage,
            'temps_passe_minutes' => $attempt->temps_passe_minutes,
            'completed_at' => $attempt->completed_at ?? date('Y-m-d H:i:s'),
        ]);
    }
}

