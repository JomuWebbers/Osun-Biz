<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Category model.
 */
class Category
{
    public int $id;
    public string $name;
    public string $slug;
    public ?string $description;
    public string $createdAt;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->name = (string) $data['name'];
        $this->slug = (string) $data['slug'];
        $this->description = $data['description'] ?? null;
        $this->createdAt = (string) $data['created_at'];
    }
}