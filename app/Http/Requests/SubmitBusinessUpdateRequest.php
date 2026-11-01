<?php

namespace App\Http\Requests;

use App\Models\Business;
use App\Models\User;

class SubmitBusinessUpdateRequest extends BusinessSubmissionRequest
{
    public function authorize(): bool
    {
        $business = $this->route('business');
        $user = $this->user();

        return $business instanceof Business
            && $user instanceof User
            && $user->can('update', $business);
    }
}
