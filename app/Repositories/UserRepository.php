<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use PDO;

class UserRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    public function findByResetToken(string $token): ?User
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users
            WHERE password_reset_token = :token AND password_reset_expires_at > NOW()
        ");
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    public function findByVerificationToken(string $token): ?User
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users
            WHERE email_verification_token = :token AND email_verification_expires_at > NOW()
        ");
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password_hash, role, status, email_verification_token, email_verification_expires_at)
            VALUES (:name, :email, :password_hash, :role, :status, :verification_token, :verification_expires)
            RETURNING id
        ");
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $data['password_hash'], PDO::PARAM_STR);
        $stmt->bindValue(':role', $data['role'] ?? 'customer', PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'] ?? 'active', PDO::PARAM_STR);
        $stmt->bindValue(':verification_token', $data['verification_token'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':verification_expires', $data['verification_expires'] ?? null, PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];
        foreach (['name', 'email', 'password_hash', 'role', 'status', 'email_verified_at', 'email_verification_token', 'email_verification_expires_at', 'password_reset_token', 'password_reset_expires_at'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = :$field";
                $params[':' . $field] = $data[$field];
            }
        }
        if (empty($fields)) {
            return false;
        }
        $fields[] = 'updated_at = NOW()';
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            if (is_int($val)) {
                $stmt->bindValue($key, $val, PDO::PARAM_INT);
            } elseif ($val === null) {
                $stmt->bindValue($key, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue($key, $val, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $this->mapRows($stmt->fetchAll());
    }

    private function mapRows(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = new User($row);
        }
        return $result;
    }
}