<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Category;
use App\Models\Location;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnerBusinessSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_dashboard_requires_authentication_and_verified_owner_access(): void
    {
        $this->get(route('owner.dashboard'))
            ->assertRedirect(route('login'));

        $unverifiedOwner = User::factory()->unverified()->create();

        $this->actingAs($unverifiedOwner)
            ->get(route('owner.dashboard'))
            ->assertRedirect(route('verification.notice'));

        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('owner.dashboard'))
            ->assertForbidden();

        $suspendedOwner = User::factory()->create([
            'role' => 'owner',
            'status' => 'suspended',
        ]);

        $this->actingAs($suspendedOwner)
            ->get(route('owner.dashboard'))
            ->assertForbidden();
    }

    public function test_owner_can_submit_a_listing_without_publishing_it(): void
    {
        $owner = User::factory()->create();
        [$category, $location] = $this->createCategoryAndLocation();
        $payload = $this->validBusinessPayload($category, $location);

        $response = $this->actingAs($owner)
            ->post(route('owner.businesses.store'), $payload);

        $submission = Submission::query()->sole();

        $response->assertRedirect(route('owner.dashboard'));
        $response->assertSessionHas('status', 'Your business listing has been submitted for review.');
        $this->assertSame($owner->id, $submission->user_id);
        $this->assertSame('pending', $submission->status);
        $this->assertSame('Osun Sample Foods', $submission->business_data['name']);
        $this->assertSame($category->name, $submission->business_data['category_name']);
        $this->assertCount(7, $submission->business_data['hours']);
        $this->assertDatabaseCount('businesses', 0);
    }

    public function test_owner_can_upload_private_listing_images(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        [$category, $location] = $this->createCategoryAndLocation();
        $payload = $this->validBusinessPayload($category, $location);
        $payload['logo'] = UploadedFile::fake()->image('logo.png');
        $payload['cover'] = UploadedFile::fake()->image('cover.jpg');
        $payload['gallery'] = [
            UploadedFile::fake()->image('gallery-one.webp'),
            UploadedFile::fake()->image('gallery-two.png'),
        ];

        $this->actingAs($owner)
            ->post(route('owner.businesses.store'), $payload)
            ->assertRedirect(route('owner.dashboard'));

        $images = Submission::query()->sole()->business_data['images'];
        $this->assertCount(4, $images);
        $this->assertSame(['logo', 'cover', 'gallery', 'gallery'], array_column($images, 'type'));

        foreach ($images as $image) {
            $this->assertStringStartsWith("business-submissions/{$owner->id}/", $image['path']);
            Storage::disk('local')->assertExists($image['path']);
        }
    }

    public function test_submission_rejects_invalid_image_types_and_oversized_images(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        [$category, $location] = $this->createCategoryAndLocation();
        $payload = $this->validBusinessPayload($category, $location);
        $payload['logo'] = UploadedFile::fake()->create('logo.txt', 10, 'text/plain');
        $payload['cover'] = UploadedFile::fake()->image('cover.jpg')->size(2049);

        $this->actingAs($owner)
            ->from(route('owner.businesses.create'))
            ->post(route('owner.businesses.store'), $payload)
            ->assertRedirect(route('owner.businesses.create'))
            ->assertSessionHasErrors(['logo', 'cover']);

        $this->assertDatabaseCount('submissions', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_gallery_uploads_cannot_exceed_six_images_in_total(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        [$category, $location] = $this->createCategoryAndLocation();
        $payload = $this->validBusinessPayload($category, $location);
        $payload['gallery'] = array_map(
            fn (int $number): UploadedFile => UploadedFile::fake()->image("gallery-{$number}.jpg"),
            range(1, 7),
        );

        $this->actingAs($owner)
            ->from(route('owner.businesses.create'))
            ->post(route('owner.businesses.store'), $payload)
            ->assertRedirect(route('owner.businesses.create'))
            ->assertSessionHasErrors('gallery');

        $this->assertDatabaseCount('submissions', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());

        $submission = $owner->submissions()->create([
            'business_data' => [
                ...$this->validBusinessPayload($category, $location),
                'images' => array_map(
                    fn (int $number): array => [
                        'type' => 'gallery',
                        'path' => "business-submissions/{$owner->id}/existing-{$number}.jpg",
                    ],
                    range(1, 5),
                ),
            ],
            'status' => 'rejected',
        ]);
        $payload['gallery'] = [
            UploadedFile::fake()->image('gallery-eight.jpg'),
            UploadedFile::fake()->image('gallery-nine.jpg'),
        ];

        $this->actingAs($owner)
            ->from(route('owner.submissions.edit', $submission))
            ->put(route('owner.submissions.update', $submission), $payload)
            ->assertRedirect(route('owner.submissions.edit', $submission))
            ->assertSessionHasErrors('gallery');

        $this->assertSame('rejected', $submission->fresh()->status);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_replacing_a_logo_deletes_the_old_file_after_resubmission(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        [$category, $location] = $this->createCategoryAndLocation();
        $oldLogoPath = "business-submissions/{$owner->id}/old-logo.png";
        $coverPath = "business-submissions/{$owner->id}/existing-cover.png";
        Storage::disk('local')->put($oldLogoPath, 'old logo');
        Storage::disk('local')->put($coverPath, 'existing cover');
        $submission = $owner->submissions()->create([
            'business_data' => [
                ...$this->validBusinessPayload($category, $location),
                'images' => [
                    ['type' => 'logo', 'path' => $oldLogoPath],
                    ['type' => 'cover', 'path' => $coverPath],
                ],
            ],
            'status' => 'rejected',
        ]);
        $payload = $this->validBusinessPayload($category, $location);
        $payload['logo'] = UploadedFile::fake()->image('new-logo.png');

        $this->actingAs($owner)
            ->put(route('owner.submissions.update', $submission), $payload)
            ->assertRedirect(route('owner.dashboard'));

        $images = $submission->fresh()->business_data['images'];
        $newLogoPath = collect($images)->firstWhere('type', 'logo')['path'];
        $this->assertNotSame($oldLogoPath, $newLogoPath);
        Storage::disk('local')->assertMissing($oldLogoPath);
        Storage::disk('local')->assertExists($newLogoPath);
        Storage::disk('local')->assertExists($coverPath);
        $this->assertSame('pending', $submission->fresh()->status);
    }

    public function test_submission_form_rejects_missing_required_fields_and_invalid_opening_hours(): void
    {
        $owner = User::factory()->create();
        [$category, $location] = $this->createCategoryAndLocation();
        $payload = $this->validBusinessPayload($category, $location);
        $payload['description'] = 'Too short';
        $payload['hours'][1]['open_time'] = '';

        $this->actingAs($owner)
            ->from(route('owner.businesses.create'))
            ->post(route('owner.businesses.store'), $payload)
            ->assertRedirect(route('owner.businesses.create'))
            ->assertSessionHasErrors(['description', 'hours.1.open_time']);

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_owner_can_edit_and_resubmit_their_rejected_submission(): void
    {
        $owner = User::factory()->create();
        [$category, $location] = $this->createCategoryAndLocation();
        $submission = $owner->submissions()->create([
            'business_data' => [
                ...$this->validBusinessPayload($category, $location),
                'category_name' => $category->name,
                'location_name' => $location->name,
            ],
            'status' => 'rejected',
            'admin_reason' => 'Please correct the description.',
        ]);

        $this->actingAs($owner)
            ->get(route('owner.submissions.edit', $submission))
            ->assertOk()
            ->assertSee('Please correct the description.')
            ->assertSee('Osun Sample Foods');

        $payload = $this->validBusinessPayload($category, $location);
        $payload['name'] = 'Updated Sample Foods';

        $this->put(route('owner.submissions.update', $submission), $payload)
            ->assertRedirect(route('owner.dashboard'));

        $submission->refresh();
        $this->assertSame('pending', $submission->status);
        $this->assertNull($submission->admin_reason);
        $this->assertSame('Updated Sample Foods', $submission->business_data['name']);
    }

    public function test_owner_cannot_read_or_edit_another_owners_submission(): void
    {
        $owner = User::factory()->create();
        $otherOwner = User::factory()->create();
        [$category, $location] = $this->createCategoryAndLocation();
        $submission = $otherOwner->submissions()->create([
            'business_data' => $this->validBusinessPayload($category, $location),
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->get(route('owner.submissions.edit', $submission))
            ->assertForbidden();

        $this->actingAs($owner)
            ->put(route('owner.submissions.update', $submission), $this->validBusinessPayload($category, $location))
            ->assertForbidden();

        $this->assertSame('pending', $submission->fresh()->status);
    }

    public function test_approved_submission_cannot_be_edited_by_its_owner(): void
    {
        $owner = User::factory()->create();
        [$category, $location] = $this->createCategoryAndLocation();
        $submission = $owner->submissions()->create([
            'business_data' => $this->validBusinessPayload($category, $location),
            'status' => 'approved',
        ]);

        $this->actingAs($owner)
            ->get(route('owner.submissions.edit', $submission))
            ->assertForbidden();
    }

    public function test_owner_can_request_changes_to_their_live_listing_for_moderation(): void
    {
        $owner = User::factory()->create();
        [$category, $location] = $this->createCategoryAndLocation();
        $business = $this->createBusiness($owner, $category, $location);
        $business->images()->create([
            'path' => 'businesses/osun-sample-foods/logo.png',
            'type' => 'logo',
            'is_primary' => true,
        ]);
        $payload = $this->validBusinessPayload($category, $location);
        $payload['name'] = 'Proposed New Business Name';

        $this->actingAs($owner)
            ->get(route('owner.businesses.edit', $business))
            ->assertOk()
            ->assertSee('Request a listing edit')
            ->assertSee('Your current listing remains public')
            ->assertSee('name="logo"', false)
            ->assertSee('name="cover"', false)
            ->assertSee('name="gallery[]"', false);

        $this->actingAs($owner)
            ->put(route('owner.businesses.update', $business), $payload)
            ->assertRedirect(route('owner.dashboard'));

        $this->assertSame('Osun Sample Foods', $business->fresh()->name);
        $this->assertDatabaseHas('submissions', [
            'user_id' => $owner->id,
            'status' => 'pending',
        ]);
        $businessData = Submission::query()->sole()->business_data;
        $this->assertSame($business->id, $businessData['business_id']);
        $this->assertSame([[
            'type' => 'logo',
            'path' => 'businesses/osun-sample-foods/logo.png',
        ]], $businessData['images']);
    }

    public function test_owner_cannot_edit_another_owners_live_listing(): void
    {
        $owner = User::factory()->create();
        $otherOwner = User::factory()->create();
        [$category, $location] = $this->createCategoryAndLocation();
        $business = $this->createBusiness($otherOwner, $category, $location);

        $this->actingAs($owner)
            ->get(route('owner.businesses.edit', $business))
            ->assertForbidden();

        $this->actingAs($owner)
            ->put(route('owner.businesses.update', $business), $this->validBusinessPayload($category, $location))
            ->assertForbidden();

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_owner_dashboard_only_shows_the_signed_in_owners_records(): void
    {
        $owner = User::factory()->create();
        $otherOwner = User::factory()->create();
        [$category, $location] = $this->createCategoryAndLocation();
        $ownBusiness = $this->createBusiness($owner, $category, $location);
        $otherBusiness = $this->createBusiness($otherOwner, $category, $location, [
            'name' => 'Another Owner Private Listing',
            'slug' => 'another-owner-private-listing',
        ]);
        $ownSubmission = $owner->submissions()->create([
            'business_data' => ['name' => 'My Pending Listing'],
            'status' => 'pending',
        ]);
        $otherOwner->submissions()->create([
            'business_data' => ['name' => 'Another Owner Private Submission'],
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee($ownBusiness->name)
            ->assertSee($ownSubmission->business_data['name'])
            ->assertDontSee($otherBusiness->name)
            ->assertDontSee('Another Owner Private Submission');
    }

    private function createCategoryAndLocation(): array
    {
        return [
            Category::query()->create([
                'name' => 'Restaurants',
                'slug' => 'restaurants-'.uniqid(),
            ]),
            Location::query()->create([
                'name' => 'Osogbo',
                'slug' => 'osogbo-'.uniqid(),
            ]),
        ];
    }

    private function validBusinessPayload(Category $category, Location $location): array
    {
        return [
            'name' => 'Osun Sample Foods',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'address' => '12 Olaiya Road, Osogbo',
            'description' => 'A locally owned business serving freshly prepared meals and catering across Osun State.',
            'services' => 'Local meals, event catering',
            'phone' => '08030000000',
            'whatsapp' => '08030000000',
            'email' => 'hello@example.com',
            'website' => 'https://example.com',
            'hours' => collect(range(0, 6))
                ->map(fn (int $day): array => [
                    'day_of_week' => $day,
                    'is_closed' => $day === 0 ? '1' : '0',
                    'open_time' => $day === 0 ? '' : '09:00',
                    'close_time' => $day === 0 ? '' : '17:00',
                ])
                ->all(),
        ];
    }

    private function createBusiness(
        User $owner,
        Category $category,
        Location $location,
        array $overrides = [],
    ): Business {
        return Business::query()->create(array_merge([
            'owner_id' => $owner->id,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'name' => 'Osun Sample Foods',
            'slug' => 'osun-sample-foods-'.uniqid(),
            'description' => 'A locally owned business serving freshly prepared meals.',
            'status' => 'approved',
            'rating' => 4.5,
            'review_count' => 1,
        ], $overrides));
    }
}
