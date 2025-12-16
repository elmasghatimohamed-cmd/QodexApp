<?php

namespace App\Repositories;
use App\Models\Result;
use PDO;

class ResultRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM results");
        $stmt->execute();
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new Result($row);
        }
        return $results;
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM results WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Result($row) : null;
    }

    public function findByEtudiant($etudiantId)
    {
        $stmt = $this->db->prepare("SELECT * FROM results WHERE etudiant_id = :etudiant_id ORDER BY completed_at DESC");
        $stmt->execute(['etudiant_id' => $etudiantId]);
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new Result($row);
        }
        return $results;
    }
    public function findByQuiz($quizId)
    {
        $stmt = $this->db->prepare("SELECT * FROM results WHERE quiz_id = :quiz_id ORDER BY completed_at DESC");
        $stmt->execute(['quiz_id' => $quizId]);
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new Result($row);
        }
        return $results;
    }

    public function findByEtudiantAndQuiz($etudiantId, $quizId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM results 
             WHERE etudiant_id = :etudiant_id AND quiz_id = :quiz_id 
             ORDER BY completed_at DESC LIMIT 1"
        );
        $stmt->execute([
            'etudiant_id' => $etudiantId,
            'quiz_id' => $quizId
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Result($row) : null;
    }

    public function getAverageScoreByQuiz($quizId)
    {
        $stmt = $this->db->prepare(
            "SELECT AVG(pourcentage) as moyenne FROM results 
             WHERE quiz_id = :quiz_id AND completed_at IS NOT NULL"
        );
        $stmt->execute(['quiz_id' => $quizId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['moyenne'] ?? 0;
    }

    public function getQuizStatistics($quizId)
    {
        $stmt = $this->db->prepare(
            "SELECT 
                COUNT(*) as total_tentatives,
                AVG(pourcentage) as moyenne,
                MAX(pourcentage) as meilleur_score,
                MIN(pourcentage) as moins_bon_score,
                AVG(temps_passe_minutes) as temps_moyen
             FROM results 
             WHERE quiz_id = :quiz_id AND completed_at IS NOT NULL"
        );
        $stmt->execute(['quiz_id' => $quizId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create(Result $result)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO results (quiz_id, etudiant_id, score, total_points, pourcentage, temps_passe_minutes, started_at, completed_at) 
             VALUES (:quiz_id, :etudiant_id, :score, :total_points, :pourcentage, :temps_passe_minutes, :started_at, :completed_at)"
        );

        $stmt->execute([
            'quiz_id' => $result->quiz_id,
            'etudiant_id' => $result->etudiant_id,
            'score' => $result->score,
            'total_points' => $result->total_points,
            'pourcentage' => $result->pourcentage,
            'temps_passe_minutes' => $result->temps_passe_minutes,
            'started_at' => $result->started_at,
            'completed_at' => $result->completed_at
        ]);

        return $this->db->lastInsertId();
    }

    public function update(Result $result)
    {
        $stmt = $this->db->prepare(
            "UPDATE results SET 
                score = :score,
                total_points = :total_points,
                pourcentage = :pourcentage,
                temps_passe_minutes = :temps_passe_minutes,
                completed_at = :completed_at
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $result->id,
            'score' => $result->score,
            'total_points' => $result->total_points,
            'pourcentage' => $result->pourcentage,
            'temps_passe_minutes' => $result->temps_passe_minutes,
            'completed_at' => $result->completed_at
        ]);
    }
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM results WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}