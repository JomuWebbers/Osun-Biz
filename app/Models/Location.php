<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Location (town) model.
 */
class Location
{
    public int $id;
    public string $name;
    public string $slug;
    public string $state;
    public string $createdAt;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->name = (string) $data['name'];
        $this->slug = (string) $data['slug'];
        $this->state = (string) ($data['state'] ?? 'Osun');
        $this->createdAt = (string) $data['created_at'];
    }
}