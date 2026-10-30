<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Business model.
 * Represents a business listing in the OsunBiz directory.
 */
class Business
{
    public int $id;
    public int $ownerId;
    public int $categoryId;
    public int $locationId;
    public string $name;
    public string $slug;
    public ?string $description;
    public ?string $address;
    public ?string $phone;
    public ?string $email;
    public ?string $website;
    public string $status;
    public float $rating;
    public int $reviewCount;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->ownerId = (int) $data['owner_id'];
        $this->categoryId = (int) $data['category_id'];
        $this->locationId = (int) $data['location_id'];
        $this->name = (string) $data['name'];
        $this->slug = (string) $data['slug'];
        $this->description = $data['description'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->website = $data['website'] ?? null;
        $this->status = (string) $data['status'];
        $this->rating = (float) ($data['rating'] ?? 0.0);
        $this->reviewCount = (int) ($data['review_count'] ?? 0);
        $this->createdAt = (string) $data['created_at'];
        $this->updatedAt = (string) $data['updated_at'];
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}