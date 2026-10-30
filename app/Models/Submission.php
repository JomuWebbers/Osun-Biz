<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Submission model.
 * Represents a pending business listing submission awaiting admin approval.
 */
class Submission
{
    public int $id;
    public int $userId;
    public array $businessData;
    public string $status;
    public ?string $adminReason;
    public ?int $reviewedBy;
    public ?string $reviewedAt;
    public string $createdAt;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->userId = (int) $data['user_id'];
        $this->businessData = is_array($data['business_data']) ? $data['business_data'] : json_decode((string) $data['business_data'], true);
        $this->status = (string) $data['status'];
        $this->adminReason = $data['admin_reason'] ?? null;
        $this->reviewedBy = $data['reviewed_by'] ?? null;
        $this->reviewedAt = $data['reviewed_at'] ?? null;
        $this->createdAt = (string) $data['created_at'];
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}