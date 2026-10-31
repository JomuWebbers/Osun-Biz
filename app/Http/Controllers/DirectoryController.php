<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'town' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'in:recent,rating,name'],
        ]);

        $businesses = $this->approvedBusinesses();

        if (filled($filters['q'] ?? null)) {
            $term = '%'.mb_strtolower(trim($filters['q'])).'%';

            $businesses->where(function (Builder $query) use ($term): void {
                $query
                    ->whereRaw('LOWER(name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(address) LIKE ?', [$term]);
            });
        }

        if (filled($filters['town'] ?? null)) {
            $businesses->whereHas(
                'location',
                fn (Builder $query) => $query->where('slug', $filters['town']),
            );
        }

        if (filled($filters['category'] ?? null)) {
            $businesses->whereHas(
                'category',
                fn (Builder $query) => $query->where('slug', $filters['category']),
            );
        }

        $this->applySort($businesses, $filters['sort'] ?? 'rating');

        return view('public.directory', [
            'businesses' => $businesses->paginate(12)->withQueryString(),
            'categories' => Category::query()->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function landing(Request $request, string $town, string $category): View
    {
        $location = Location::query()->where('slug', $town)->firstOrFail();
        $categoryModel = Category::query()->where('slug', $category)->firstOrFail();
        $filters = $request->validate([
            'sort' => ['nullable', 'in:recent,rating,name'],
        ]);

        $businesses = $this->approvedBusinesses()
            ->where('location_id', $location->id)
            ->where('category_id', $categoryModel->id);

        $this->applySort($businesses, $filters['sort'] ?? 'rating');

        $otherCategories = Category::query()
            ->whereKeyNot($categoryModel->id)
            ->withCount([
                'businesses as matching_businesses_count' => fn (Builder $query) => $query
                    ->where('status', 'approved')
                    ->where('location_id', $location->id),
            ])
            ->orderBy('name')
            ->get();

        $otherLocations = Location::query()
            ->whereKeyNot($location->id)
            ->withCount([
                'businesses as matching_businesses_count' => fn (Builder $query) => $query
                    ->where('status', 'approved')
                    ->where('category_id', $categoryModel->id),
            ])
            ->orderByDesc('matching_businesses_count')
            ->orderBy('name')
            ->limit(6)
            ->get();

        return view('public.landing', [
            'location' => $location,
            'category' => $categoryModel,
            'businesses' => $businesses->paginate(12)->withQueryString(),
            'otherCategories' => $otherCategories,
            'otherLocations' => $otherLocations,
            'sort' => $filters['sort'] ?? 'rating',
        ]);
    }

    public function show(string $slug): View
    {
        $business = Business::query()
            ->where('status', 'approved')
            ->where('slug', $slug)
            ->with(['category', 'location', 'hours'])
            ->withCount([
                'reviews' => fn (Builder $query) => $query->where('status', 'approved'),
            ])
            ->firstOrFail();

        $reviews = $business->reviews()
            ->where('status', 'approved')
            ->with('user')
            ->latest()
            ->limit(3)
            ->get();

        $similarBusinesses = $this->approvedBusinesses()
            ->where('category_id', $business->category_id)
            ->where('location_id', $business->location_id)
            ->whereKeyNot($business->id)
            ->orderByDesc('rating')
            ->limit(3)
            ->get();

        return view('public.business', [
            'business' => $business,
            'reviews' => $reviews,
            'similarBusinesses' => $similarBusinesses,
        ]);
    }

    public function reviews(string $slug): View
    {
        $business = Business::query()
            ->where('status', 'approved')
            ->where('slug', $slug)
            ->with(['category', 'location'])
            ->withCount([
                'reviews' => fn (Builder $query) => $query->where('status', 'approved'),
            ])
            ->firstOrFail();

        $reviews = $business->reviews()
            ->where('status', 'approved')
            ->with('user')
            ->latest()
            ->paginate(12);

        return view('public.reviews', [
            'business' => $business,
            'reviews' => $reviews,
        ]);
    }

    private function approvedBusinesses(): Builder
    {
        return Business::query()
            ->where('status', 'approved')
            ->with(['category', 'location'])
            ->withCount([
                'reviews' => fn (Builder $query) => $query->where('status', 'approved'),
            ]);
    }

    private function applySort(Builder $businesses, string $sort): void
    {
        match ($sort) {
            'recent' => $businesses->latest(),
            'name' => $businesses->orderBy('name'),
            default => $businesses->orderByDesc('rating')->orderByDesc('review_count')->latest(),
        };
    }
}
