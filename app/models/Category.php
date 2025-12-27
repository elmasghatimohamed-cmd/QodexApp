<?php

namespace App\Models;

class Category
{
    public $id;
    public $name;
    public $description;
    public $enseignant_id;
    public $created_at;
    public $updated_at;
    public $deleted_at;

    public function __construct(array $data = [])
    {
        $this->id = $data["id"] ?? null;
        $this->name = $data["name"] ?? null;
        $this->description = $data["description"] ?? null;
        $this->enseignant_id = $data["enseignant_id"] ?? null;
        $this->created_at = $data["created_at"] ?? null;
        $this->updated_at = $data["updated_at"] ?? null;
        $this->deleted_at = $data["deleted_at"] ?? null;
    }
}