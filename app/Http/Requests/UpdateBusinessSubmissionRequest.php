<?php

namespace App\Http\Requests;

use App\Models\Submission;
use App\Models\User;

class UpdateBusinessSubmissionRequest extends BusinessSubmissionRequest
{
    public function authorize(): bool
    {
        $submission = $this->route('submission');
        $user = $this->user();

        return $submission instanceof Submission
            && $user instanceof User
            && $user->can('update', $submission);
    }
}
