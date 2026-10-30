<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Location;
use PDO;

class LocationRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM locations ORDER BY name ASC");
        return $this->mapRows($stmt->fetchAll());
    }

    public function findById(int $id): ?Location
    {
        $stmt = $this->db->prepare("SELECT * FROM locations WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? new Location($row) : null;
    }

    public function findBySlug(string $slug): ?Location
    {
        $stmt = $this->db->prepare("SELECT * FROM locations WHERE slug = :slug");
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? new Location($row) : null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO locations (name, slug, state)
            VALUES (:name, :slug, :state)
            RETURNING id
        ");
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':slug', $data['slug'], PDO::PARAM_STR);
        $stmt->bindValue(':state', $data['state'] ?? 'Osun', PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];
        foreach (['name', 'slug', 'state'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = :$field";
                $params[':' . $field] = $data[$field];
            }
        }
        if (empty($fields)) {
            return false;
        }
        $sql = "UPDATE locations SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM locations WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    private function mapRows(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = new Location($row);
        }
        return $result;
    }
}