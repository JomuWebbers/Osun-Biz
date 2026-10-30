<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Business;
use PDO;

/**
 * BusinessRepository — data access for businesses.
 */
class BusinessRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function findAll(int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->db->prepare("
            SELECT b.*, c.name AS category_name, l.name AS location_name
            FROM businesses b
            JOIN categories c ON b.category_id = c.id
            JOIN locations l ON b.location_id = l.id
            WHERE b.status = 'approved'
            ORDER BY b.rating DESC, b.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $this->mapRows($stmt->fetchAll());
    }

    public function findById(int $id): ?Business
    {
        $stmt = $this->db->prepare("
            SELECT b.*, c.name AS category_name, l.name AS location_name
            FROM businesses b
            JOIN categories c ON b.category_id = c.id
            JOIN locations l ON b.location_id = l.id
            WHERE b.id = :id
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? new Business($row) : null;
    }

    public function findBySlug(string $slug): ?Business
    {
        $stmt = $this->db->prepare("
            SELECT b.*, c.name AS category_name, l.name AS location_name
            FROM businesses b
            JOIN categories c ON b.category_id = c.id
            JOIN locations l ON b.location_id = l.id
            WHERE b.slug = :slug AND b.status = 'approved'
        ");
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? new Business($row) : null;
    }

    public function search(
        string $query = '',
        string $location = '',
        string $category = '',
        string $sort = 'rating',
        int $limit = 20,
        int $offset = 0
    ): array {
        $sql = "
            SELECT b.*, c.name AS category_name, l.name AS location_name
            FROM businesses b
            JOIN categories c ON b.category_id = c.id
            JOIN locations l ON b.location_id = l.id
            WHERE b.status = 'approved'
        ";
        $params = [];
        if ($query !== '') {
            $sql .= " AND (b.name ILIKE :query OR b.description ILIKE :query)";
            $params[':query'] = '%' . $query . '%';
        }
        if ($location !== '') {
            $sql .= " AND l.slug = :location";
            $params[':location'] = $location;
        }
        if ($category !== '') {
            $sql .= " AND c.slug = :category";
            $params[':category'] = $category;
        }
        $sql .= " ORDER BY b." . ($sort === 'name' ? 'name' : 'rating') . " " . ($sort === 'name' ? 'ASC' : 'DESC');
        $sql .= " LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $this->mapRows($stmt->fetchAll());
    }

    public function searchCount(string $query = '', string $location = '', string $category = ''): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM businesses b
            JOIN categories c ON b.category_id = c.id
            JOIN locations l ON b.location_id = l.id
            WHERE b.status = 'approved'
        ";
        $params = [];
        if ($query !== '') {
            $sql .= " AND (b.name ILIKE :query OR b.description ILIKE :query)";
            $params[':query'] = '%' . $query . '%';
        }
        if ($location !== '') {
            $sql .= " AND l.slug = :location";
            $params[':location'] = $location;
        }
        if ($category !== '') {
            $sql .= " AND c.slug = :category";
            $params[':category'] = $category;
        }
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function findByOwner(int $ownerId): array
    {
        $stmt = $this->db->prepare("
            SELECT b.*, c.name AS category_name, l.name AS location_name
            FROM businesses b
            JOIN categories c ON b.category_id = c.id
            JOIN locations l ON b.location_id = l.id
            WHERE b.owner_id = :owner_id
            ORDER BY b.created_at DESC
        ");
        $stmt->bindValue(':owner_id', $ownerId, PDO::PARAM_INT);
        $stmt->execute();
        return $this->mapRows($stmt->fetchAll());
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO businesses (owner_id, category_id, location_id, name, slug, description, address, phone, email, website, status, rating, review_count)
            VALUES (:owner_id, :category_id, :location_id, :name, :slug, :description, :address, :phone, :email, :website, :status, 0.00, 0)
            RETURNING id
        ");
        $stmt->bindValue(':owner_id', $data['owner_id'], PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindValue(':location_id', $data['location_id'], PDO::PARAM_INT);
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':slug', $data['slug'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':address', $data['address'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':phone', $data['phone'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':website', $data['website'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'] ?? 'pending', PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];
        $allowed = ['name', 'slug', 'description', 'address', 'phone', 'email', 'website', 'status', 'category_id', 'location_id'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = :$field";
                $params[':' . $field] = $data[$field];
            }
        }
        if (empty($fields)) {
            return false;
        }
        $fields[] = 'updated_at = NOW()';
        $sql = "UPDATE businesses SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            if (is_int($val)) {
                $stmt->bindValue($key, $val, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $val, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM businesses WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function updateRating(int $businessId, float $rating, int $reviewCount): bool
    {
        $stmt = $this->db->prepare("
            UPDATE businesses SET rating = :rating, review_count = :review_count, updated_at = NOW() WHERE id = :id
        ");
        $stmt->bindValue(':id', $businessId, PDO::PARAM_INT);
        $stmt->bindValue(':rating', $rating, PDO::PARAM_STR);
        $stmt->bindValue(':review_count', $reviewCount, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    private function mapRows(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = new Business($row);
        }
        return $result;
    }
}