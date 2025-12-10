<?php

namespace App\Models;
class Quiz
{
    public $id;
    public $title;
    public $description;
    public $categorie_id;
    public $enseignant_id;
    public $status;
    public $duration;
    public $created_at;
    public $updated_at;
    public $deleted_at;

    public function __construct(array $data = [])
    {
        $this->id = $data["id"] ?? null;
        $this->title = $data["title"] ?? null;
        $this->description = $data["description"] ?? null;
        $this->categorie_id = $data["categorie_id"] ?? null;
        $this->enseignant_id = $data["enseignant_id"] ?? null;
        $this->status = $data["status"] ?? "actif";
        $this->duration = $data["duration"] ?? 30;
        $this->created_at = $data["created_at"] ?? null;
        $this->updated_at = $data["updated_at"] ?? null;
        $this->deleted_at = $data["deleted_at"] ?? null;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }
    public function setDuration(int $minutes)
    {
        $this->duration = $minutes;
    }
}
