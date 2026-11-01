<?php

namespace App\Http\Requests;

use App\Models\Submission;

class StoreBusinessSubmissionRequest extends BusinessSubmissionRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Submission::class) ?? false;
    }
}
