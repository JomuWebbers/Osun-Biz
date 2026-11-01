<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'owner' && $user->status === 'active';
    }

    public function view(User $user, Submission $submission): bool
    {
        return $this->owns($user, $submission);
    }

    public function create(User $user): bool
    {
        return $user->role === 'owner' && $user->status === 'active';
    }

    public function update(User $user, Submission $submission): bool
    {
        return $this->owns($user, $submission)
            && in_array($submission->status, ['pending', 'rejected'], true);
    }

    private function owns(User $user, Submission $submission): bool
    {
        return $user->role === 'owner'
            && $user->status === 'active'
            && $submission->user_id === $user->id;
    }
}
