<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Category;
use App\Models\Location;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DirectoryDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_directory_schema_keeps_business_and_review_fields(): void
    {
        $this->assertTrue(Schema::hasColumns('businesses', [
            'id',
            'owner_id',
            'category_id',
            'location_id',
            'name',
            'slug',
            'description',
            'address',
            'phone',
            'email',
            'website',
            'status',
            'rating',
            'review_count',
            'created_at',
            'updated_at',
        ]));

        $this->assertTrue(Schema::hasColumns('business_images', [
            'id',
            'business_id',
            'path',
            'type',
            'is_primary',
            'created_at',
            'updated_at',
        ]));

        $this->assertTrue(Schema::hasColumns('business_hours', [
            'id',
            'business_id',
            'day_of_week',
            'open_time',
            'close_time',
            'is_closed',
        ]));

        $this->assertTrue(Schema::hasColumns('reviews', [
            'id',
            'business_id',
            'user_id',
            'rating',
            'comment',
            'status',
            'owner_reply',
            'owner_replied_at',
            'created_at',
            'updated_at',
        ]));

        $this->assertTrue(Schema::hasColumns('submissions', [
            'id',
            'user_id',
            'business_data',
            'status',
            'admin_reason',
            'reviewed_by',
            'reviewed_at',
            'created_at',
        ]));
    }

    public function test_database_seeder_adds_starter_categories_and_towns(): void
    {
        $this->seed();

        $this->assertDatabaseCount('categories', 8);
        $this->assertDatabaseCount('locations', 10);
        $this->assertDatabaseHas('locations', [
            'name' => 'Osogbo',
            'slug' => 'osogbo',
            'state' => 'Osun',
        ]);
    }

    public function test_business_and_review_models_expose_their_relationships(): void
    {
        $owner = User::factory()->create();
        $reviewer = User::factory()->create();
        $category = Category::query()->create([
            'name' => 'Restaurants',
            'slug' => 'restaurants-test',
        ]);
        $location = Location::query()->create([
            'name' => 'Osogbo',
            'slug' => 'osogbo-test',
        ]);

        $business = Business::query()->create([
            'owner_id' => $owner->id,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'name' => 'Example Restaurant',
            'slug' => 'example-restaurant',
        ]);
        $review = Review::query()->create([
            'business_id' => $business->id,
            'user_id' => $reviewer->id,
            'rating' => 5,
            'comment' => 'Great service',
            'owner_reply' => 'Thank you',
            'owner_replied_at' => now(),
        ]);

        $this->assertTrue($business->owner->is($owner));
        $this->assertTrue($business->category->is($category));
        $this->assertTrue($business->location->is($location));
        $this->assertTrue($review->business->is($business));
        $this->assertTrue($review->user->is($reviewer));
        $this->assertNotNull($review->owner_replied_at);
    }
}
