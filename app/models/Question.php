<?php

namespace App\Models;

class Question
{
    public $id;
    public $quiz_id;
    public $text;
    public $type_question;
    public $points;
    public $ordre;
    public $created_at;
    public $deleted_at;

    public function __construct(array $data = [])
    {
        $this->id = $data["id"] ?? null;
        $this->quiz_id = $data["quiz_id"] ?? null;
        $this->text = $data["text"] ?? null;
        $this->type_question = $data["type_question"] ?? "qcm";
        $this->points = $data["points"] ?? 1;
        $this->ordre = $data["ordre"] ?? 0;
        $this->created_at = $data["created_at"] ?? null;
        $this->deleted_at = $data["deleted_at"] ?? null;
    }

    public function setOrder(int $ordre): void
    {
        $this->ordre = $ordre;
    }

    public function setType(string $type): void
    {
        $this->type_question = $type;
    }
}