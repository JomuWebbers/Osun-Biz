<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $owner = $request->user();
        $businesses = Business::query()
            ->whereBelongsTo($owner, 'owner')
            ->with(['category', 'location'])
            ->latest()
            ->get();
        $submissions = Submission::query()
            ->whereBelongsTo($owner)
            ->latest('created_at')
            ->get();

        return view('owner.dashboard', [
            'businesses' => $businesses,
            'submissions' => $submissions,
            'pendingCount' => $submissions->where('status', 'pending')->count(),
            'rejectedCount' => $submissions->where('status', 'rejected')->count(),
        ]);
    }
}
