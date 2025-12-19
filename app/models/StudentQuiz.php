<?php

namespace App\Models;

class StudentQuiz
{
    public $id;
    public $quiz_id;
    public $etudiant_id;
    public $score;
    public $total_points;
    public $pourcentage;
    public $temps_passe_minutes;
    public $started_at;
    public $completed_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->quiz_id = $data['quiz_id'] ?? null;
        $this->etudiant_id = $data['etudiant_id'] ?? null;
        $this->score = $data['score'] ?? 0;
        $this->total_points = $data['total_points'] ?? 0;
        $this->pourcentage = $data['pourcentage'] ?? 0;
        $this->temps_passe_minutes = $data['temps_passe_minutes'] ?? 0;
        $this->started_at = $data['started_at'] ?? null;
        $this->completed_at = $data['completed_at'] ?? null;
    }

    public function updateScore(int $score, int $totalPoints): void
    {
        $this->score = $score;
        $this->total_points = $totalPoints;
        $this->pourcentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;
    }
}

