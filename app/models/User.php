<?php

namespace App\Models;

class User
{
    public $id;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $role;
    public $created_at;
    public $updated_at;
    public $deleted_at;
    public $last_login;

    public function __construct(
        string $email,
        string $password,
        string $first_name,
        string $last_name,
        string $role,
        $id = null,
        $created_at = null,
        $updated_at = null,
        $deleted_at = null,
        $last_login = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->role = $role;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->deleted_at = $deleted_at;
        $this->last_login = $last_login;
    }
}