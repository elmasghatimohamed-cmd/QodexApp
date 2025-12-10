<?php

class Result
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
        $this->id = $data["id"] ?? null;
        $this->quiz_id = $data["quiz_id"] ?? null;
        $this->etudiant_id = $data["etudiant_id"] ?? null;
        $this->score = $data["score"] ?? 0;
        $this->total_points = $data["total_points"] ?? 0;
        $this->pourcentage = $data["pourcentage"] ?? 0;
        $this->temps_passe_minutes = $data["temps_passe_minutes"] ?? 0;
        $this->started_at = $data["started_at"] ?? null;
        $this->completed_at = $data["completed_at"] ?? null;
    }

    public function updateScore(int $score, int $totalPoints)
    {
        $this->score = $score;
        $this->total_points = $totalPoints;

        if ($totalPoints > 0) {
            $this->pourcentage = ($score / $totalPoints) * 100;
        } else {
            $this->pourcentage = 0;
        }
    }

    public function complete(int $tempsPasse)
    {
        $this->completed_at = date("Y-m-d H:i:s");
        $this->temps_passe_minutes = $tempsPasse;
    }
}
