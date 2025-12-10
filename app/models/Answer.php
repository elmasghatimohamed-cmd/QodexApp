<?php

namespace App\Models;
class Answer
{
    public $id;
    public $question_id;
    public $text;
    public $is_correct;
    public $created_at;
    public $deleted_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->question_id = $data['question_id'] ?? null;
        $this->text = $data['text'] ?? null;
        $this->is_correct = $data['is_correct'] ?? false;
        $this->created_at = $data['created_at'] ?? null;
        $this->deleted_at = $data['deleted_at'] ?? null;
    }

    public function setCorrect(bool $correct)
    {
        $this->is_correct = $correct;
    }
}
