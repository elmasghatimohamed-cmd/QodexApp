<?php

namespace App\Repositories;

require_once __DIR__ . "/config/database.php";


class ResultRepository
{

    public function getAllResults($is)
    {
        $db = new Database();
        $stmt = $this->db->p("SELECT * FROM student_quizzes");
        $stmt->execute();
        $allResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print_r($allResult);
    }
}
