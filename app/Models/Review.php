<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Review model.
 * Represents a customer review for a business.
 */
class Review
{
    public int $id;
    public int $businessId;
    public int $userId;
    public int $rating;
    public ?string $comment;
    public string $status;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->businessId = (int) $data['business_id'];
        $this->userId = (int) $data['user_id'];
        $this->rating = (int) $data['rating'];
        $this->comment = $data['comment'] ?? null;
        $this->status = (string) $data['status'];
        $this->createdAt = (string) $data['created_at'];
        $this->updatedAt = (string) $data['updated_at'];
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isReported(): bool
    {
        return $this->status === 'reported';
    }
}