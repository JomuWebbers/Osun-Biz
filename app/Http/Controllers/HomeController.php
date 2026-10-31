<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categories = Category::query()
            ->withCount([
                'businesses' => fn (Builder $query) => $query->where('status', 'approved'),
            ])
            ->orderBy('name')
            ->get();

        $locations = Location::query()
            ->withCount([
                'businesses' => fn (Builder $query) => $query->where('status', 'approved'),
            ])
            ->orderByDesc('businesses_count')
            ->orderBy('name')
            ->get();

        $featuredBusinesses = Business::query()
            ->where('status', 'approved')
            ->with(['category', 'location'])
            ->withCount([
                'reviews' => fn (Builder $query) => $query->where('status', 'approved'),
            ])
            ->orderByDesc('rating')
            ->orderByDesc('review_count')
            ->latest()
            ->limit(6)
            ->get();

        return view('public.home', [
            'categories' => $categories,
            'locations' => $locations,
            'popularLocations' => $locations->take(8),
            'featuredBusinesses' => $featuredBusinesses,
            'businessCount' => Business::query()->where('status', 'approved')->count(),
            'categoryCount' => $categories->count(),
            'locationCount' => $locations->count(),
        ]);
    }
}
