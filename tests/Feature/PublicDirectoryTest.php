<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Category;
use App\Models\Location;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_real_directory_counts_without_invented_listings(): void
    {
        $category = $this->createCategory();
        $location = $this->createLocation();
        $this->createBusiness($category, $location, [
            'name' => 'Approved Osun Shop',
            'slug' => 'approved-osun-shop',
            'status' => 'approved',
        ]);
        $this->createBusiness($category, $location, [
            'name' => 'Pending Osun Shop',
            'slug' => 'pending-osun-shop',
            'status' => 'pending',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Approved Osun Shop')
            ->assertDontSee('Pending Osun Shop')
            ->assertSee('1</strong> approved businesses', false)
            ->assertSee('Restaurants');
    }

    public function test_directory_search_filters_approved_businesses_by_keyword_town_and_category(): void
    {
        $category = $this->createCategory();
        $location = $this->createLocation();
        $matching = $this->createBusiness($category, $location, [
            'name' => 'Osogbo Phone Repairs',
            'slug' => 'osogbo-phone-repairs',
            'description' => 'Mobile phone and tablet repairs',
            'status' => 'approved',
        ]);
        $this->createBusiness($category, $location, [
            'name' => 'Another Repair Shop',
            'slug' => 'another-repair-shop',
            'status' => 'approved',
        ]);
        $this->createBusiness($category, $location, [
            'name' => 'Pending Osogbo Phone Repairs',
            'slug' => 'pending-osogbo-phone-repairs',
            'status' => 'pending',
        ]);

        $this->get(route('directory.index', [
            'q' => 'PHONE',
            'town' => $location->slug,
            'category' => $category->slug,
        ]))
            ->assertOk()
            ->assertSee($matching->name)
            ->assertDontSee('Another Repair Shop')
            ->assertDontSee('Pending Osogbo Phone Repairs');
    }

    public function test_homepage_search_includes_towns_beyond_the_popular_locations_section(): void
    {
        for ($number = 1; $number <= 10; $number++) {
            $this->createLocation([
                'name' => 'Town '.$number,
                'slug' => 'town-'.$number,
            ]);
        }

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Town 10')
            ->assertSee('10</strong> towns', false);
    }

    public function test_town_category_landing_shows_matching_listings_and_alternate_locations(): void
    {
        $category = $this->createCategory();
        $location = $this->createLocation();
        $otherLocation = $this->createLocation([
            'name' => 'Ile-Ife',
            'slug' => 'ile-ife',
        ]);
        $matching = $this->createBusiness($category, $location, [
            'name' => 'Osogbo Cafe',
            'slug' => 'osogbo-cafe',
            'status' => 'approved',
        ]);
        $this->createBusiness($category, $otherLocation, [
            'name' => 'Ile-Ife Cafe',
            'slug' => 'ile-ife-cafe',
            'status' => 'approved',
        ]);

        $this->get(route('directory.landing', [$location->slug, $category->slug]))
            ->assertOk()
            ->assertSee('Restaurants in Osogbo')
            ->assertSee($matching->name)
            ->assertSee('Ile-Ife')
            ->assertDontSee('Ile-Ife Cafe');
    }

    public function test_business_details_show_approved_reviews_and_hide_unapproved_content(): void
    {
        $category = $this->createCategory();
        $location = $this->createLocation();
        $business = $this->createBusiness($category, $location, [
            'name' => 'Osun Market',
            'slug' => 'osun-market',
            'status' => 'approved',
        ]);
        $reviewer = User::factory()->create();

        Review::query()->create([
            'business_id' => $business->id,
            'user_id' => $reviewer->id,
            'rating' => 5,
            'comment' => 'Helpful local service',
            'status' => 'approved',
            'owner_reply' => 'Thank you for visiting',
            'owner_replied_at' => now(),
        ]);
        Review::query()->create([
            'business_id' => $business->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 1,
            'comment' => 'Private pending feedback',
            'status' => 'pending',
        ]);

        $this->get(route('directory.show', $business->slug))
            ->assertOk()
            ->assertSee('Helpful local service')
            ->assertSee('Thank you for visiting')
            ->assertDontSee('Private pending feedback');
    }

    public function test_public_routes_do_not_expose_pending_businesses_or_unknown_landing_slugs(): void
    {
        $category = $this->createCategory();
        $location = $this->createLocation();
        $pending = $this->createBusiness($category, $location, [
            'status' => 'pending',
        ]);

        $this->get(route('directory.show', $pending->slug))->assertNotFound();
        $this->get(route('directory.landing', ['unknown-town', $category->slug]))->assertNotFound();
        $this->get(route('directory.landing', [$location->slug, 'unknown-category']))->assertNotFound();
    }

    public function test_public_reviews_page_only_shows_approved_reviews(): void
    {
        $category = $this->createCategory();
        $location = $this->createLocation();
        $business = $this->createBusiness($category, $location, [
            'status' => 'approved',
        ]);

        Review::query()->create([
            'business_id' => $business->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 4,
            'comment' => 'Visible approved comment',
            'status' => 'approved',
        ]);
        Review::query()->create([
            'business_id' => $business->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 2,
            'comment' => 'Hidden pending comment',
            'status' => 'pending',
        ]);

        $this->get(route('directory.reviews', $business->slug))
            ->assertOk()
            ->assertSee('Visible approved comment')
            ->assertDontSee('Hidden pending comment');
    }

    private function createCategory(array $attributes = []): Category
    {
        return Category::query()->create(array_merge([
            'name' => 'Restaurants',
            'slug' => 'restaurants',
            'description' => 'Food and dining',
        ], $attributes));
    }

    private function createLocation(array $attributes = []): Location
    {
        return Location::query()->create(array_merge([
            'name' => 'Osogbo',
            'slug' => 'osogbo',
            'state' => 'Osun',
        ], $attributes));
    }

    private function createBusiness(Category $category, Location $location, array $attributes = []): Business
    {
        return Business::query()->create(array_merge([
            'owner_id' => User::factory()->create()->id,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'name' => 'Test Business',
            'slug' => 'test-business-'.uniqid(),
            'description' => 'A sample description',
            'status' => 'approved',
            'rating' => 4.5,
            'review_count' => 0,
        ], $attributes));
    }
}
