<?php

class StudentAnswer
{
    public $id;
    public $student_quiz_id;
    public $question_id;
    public $answer_id;
    public $reponse_texte;
    public $is_correct;
    public $created_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->student_quiz_id = $data['student_quiz_id'] ?? null;
        $this->question_id = $data['question_id'] ?? null;
        $this->answer_id = $data['answer_id'] ?? null;
        $this->reponse_texte = $data['reponse_texte'] ?? null;
        $this->is_correct = $data['is_correct'] ?? false;
        $this->created_at = $data['created_at'] ?? null;
    }

    public function setCorrect(bool $correct)
    {
        $this->is_correct = $correct;
    }
}
