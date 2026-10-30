<?php

declare(strict_types=1);

namespace App\Models;

/**
 * User model.
 * Represents a customer, business owner, or administrator.
 */
class User
{
    public int $id;
    public string $name;
    public string $email;
    public string $passwordHash;
    public string $role;
    public string $status;
    public ?string $emailVerifiedAt;
    public ?string $emailVerificationToken;
    public ?string $passwordResetToken;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->name = (string) $data['name'];
        $this->email = (string) $data['email'];
        $this->passwordHash = (string) $data['password_hash'];
        $this->role = (string) $data['role'];
        $this->status = (string) $data['status'];
        $this->emailVerifiedAt = $data['email_verified_at'] ?? null;
        $this->emailVerificationToken = $data['email_verification_token'] ?? null;
        $this->passwordResetToken = $data['password_reset_token'] ?? null;
        $this->createdAt = (string) $data['created_at'];
        $this->updatedAt = (string) $data['updated_at'];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }
}