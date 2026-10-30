<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use PDO;

class CategoryRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name ASC");
        return $this->mapRows($stmt->fetchAll());
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? new Category($row) : null;
    }

    public function findBySlug(string $slug): ?Category
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = :slug");
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? new Category($row) : null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO categories (name, slug, description)
            VALUES (:name, :slug, :description)
            RETURNING id
        ");
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':slug', $data['slug'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'] ?? null, PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];
        foreach (['name', 'slug', 'description'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = :$field";
                $params[':' . $field] = $data[$field];
            }
        }
        if (empty($fields)) {
            return false;
        }
        $sql = "UPDATE categories SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    private function mapRows(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = new Category($row);
        }
        return $result;
    }
}