<?php

namespace App\Repositories;

use App\Models\User;
use PDO;

class UserRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE deleted_at IS NULL");
        $stmt->execute();
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
                $row['email'],
                $row['password'],
                $row['first_name'],
                $row['last_name'],
                $row['role'],
                $row['id'],
                $row['created_at'],
                $row['updated_at'],
                $row['deleted_at'],
                $row['last_login']
            );
        }
        return $users;
    }

    public function findById($id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new User(
                $row['email'],
                $row['password'],
                $row['first_name'],
                $row['last_name'],
                $row['role'],
                $row['id'],
                $row['created_at'],
                $row['updated_at'],
                $row['deleted_at'],
                $row['last_login']
            );
        }
        return null;
    }

    public function findByEmail($email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND deleted_at IS NULL");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new User(
                $row['email'],
                $row['password'],
                $row['first_name'],
                $row['last_name'],
                $row['role'],
                $row['id'],
                $row['created_at'],
                $row['updated_at'],
                $row['deleted_at'],
                $row['last_login']
            );
        }
        return null;
    }

    public function findByRole($role): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = :role AND deleted_at IS NULL");
        $stmt->execute(['role' => $role]);
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
                $row['email'],
                $row['password'],
                $row['first_name'],
                $row['last_name'],
                $row['role'],
                $row['id'],
                $row['created_at'],
                $row['updated_at'],
                $row['deleted_at'],
                $row['last_login']
            );
        }
        return $users;
    }

    public function create(User $user): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (email, password, first_name, last_name, role, created_at) 
             VALUES (:email, :password, :first_name, :last_name, :role, NOW())"
        );

        $stmt->execute([
            'email' => $user->email,
            'password' => $user->password,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->role
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(User $user): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET 
                email = :email,
                password = :password,
                first_name = :first_name,
                last_name = :last_name,
                role = :role,
                updated_at = NOW()
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $user->id,
            'email' => $user->email,
            'password' => $user->password,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->role
        ]);
    }

    public function updateLastLogin($id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function delete($id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}