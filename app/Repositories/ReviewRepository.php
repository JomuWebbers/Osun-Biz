<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Review;
use PDO;

class ReviewRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function findByBusiness(int $businessId, string $status = 'approved'): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, u.name AS user_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.business_id = :business_id AND r.status = :status
            ORDER BY r.created_at DESC
        ");
        $stmt->bindValue(':business_id', $businessId, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
        return $this->mapRows($stmt->fetchAll());
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, b.name AS business_name
            FROM reviews r
            JOIN businesses b ON r.business_id = b.id
            WHERE r.user_id = :user_id
            ORDER BY r.created_at DESC
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $this->mapRows($stmt->fetchAll());
    }

    public function findPending(): array
    {
        $stmt = $this->db->query("
            SELECT r.*, b.name AS business_name, u.name AS user_name
            FROM reviews r
            JOIN businesses b ON r.business_id = b.id
            JOIN users u ON r.user_id = u.id
            WHERE r.status = 'pending'
            ORDER BY r.created_at DESC
        ");
        return $this->mapRows($stmt->fetchAll());
    }

    public function findReported(): array
    {
        $stmt = $this->db->query("
            SELECT r.*, b.name AS business_name, u.name AS user_name
            FROM reviews r
            JOIN businesses b ON r.business_id = b.id
            JOIN users u ON r.user_id = u.id
            WHERE r.status = 'reported'
            ORDER BY r.created_at DESC
        ");
        return $this->mapRows($stmt->fetchAll());
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO reviews (business_id, user_id, rating, comment, status)
            VALUES (:business_id, :user_id, :rating, :comment, 'pending')
            RETURNING id
        ");
        $stmt->bindValue(':business_id', $data['business_id'], PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':rating', $data['rating'], PDO::PARAM_INT);
        $stmt->bindValue(':comment', $data['comment'] ?? null, PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function updateStatus(int $id, string $status, ?string $adminReason = null): bool
    {
        $stmt = $this->db->prepare("
            UPDATE reviews SET status = :status, updated_at = NOW() WHERE id = :id
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function updateBusinessRating(int $businessId): void
    {
        $stmt = $this->db->prepare("
            SELECT AVG(rating)::float AS avg_rating, COUNT(*) AS cnt
            FROM reviews
            WHERE business_id = :business_id AND status = 'approved'
        ");
        $stmt->bindValue(':business_id', $businessId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row && $row['cnt'] > 0) {
            $update = $this->db->prepare("
                UPDATE businesses SET rating = :rating, review_count = :count, updated_at = NOW() WHERE id = :id
            ");
            $update->bindValue(':id', $businessId, PDO::PARAM_INT);
            $update->bindValue(':rating', (float) $row['avg_rating'], PDO::PARAM_STR);
            $update->bindValue(':count', (int) $row['cnt'], PDO::PARAM_INT);
            $update->execute();
        }
    }

    private function mapRows(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = new Review($row);
        }
        return $result;
    }
}