<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

class BusinessPolicy
{
    public function update(User $user, Business $business): bool
    {
        return $user->role === 'owner'
            && $user->status === 'active'
            && $business->owner_id === $user->id;
    }
}
