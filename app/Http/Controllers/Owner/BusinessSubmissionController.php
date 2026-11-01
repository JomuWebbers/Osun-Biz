<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBusinessSubmissionRequest;
use App\Http\Requests\SubmitBusinessUpdateRequest;
use App\Http\Requests\UpdateBusinessSubmissionRequest;
use App\Models\Business;
use App\Models\Category;
use App\Models\Location;
use App\Models\Submission;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class BusinessSubmissionController extends Controller
{
    public function create(): View
    {
        return view('owner.business-form', $this->formOptions());
    }

    public function store(StoreBusinessSubmissionRequest $request): RedirectResponse
    {
        [$businessData] = $this->submissionData(
            $request->validated(),
            $request->user()->id,
        );

        $this->persistWithImageCleanup(
            fn (): Submission => $request->user()->submissions()->create([
                'business_data' => $businessData,
                'status' => 'pending',
            ]),
            $this->newImagePaths($businessData),
        );

        return redirect()
            ->route('owner.dashboard')
            ->with('status', 'Your business listing has been submitted for review.');
    }

    public function editSubmission(Submission $submission): View
    {
        Gate::authorize('update', $submission);

        return view('owner.business-form', $this->formOptions(
            submission: $submission,
            values: $submission->business_data,
        ));
    }

    public function updateSubmission(
        UpdateBusinessSubmissionRequest $request,
        Submission $submission,
    ): RedirectResponse {
        [$data, $removedImages] = $this->submissionData(
            $request->validated(),
            $request->user()->id,
            $submission->business_data['images'] ?? [],
            data_get($submission->business_data, 'business_id'),
        );

        $this->persistWithImageCleanup(
            fn (): bool => $submission->update([
                'business_data' => $data,
                'status' => 'pending',
                'admin_reason' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]),
            $this->newImagePaths($data, $submission->business_data['images'] ?? []),
        );

        $this->deleteStoredImages($removedImages);

        return redirect()
            ->route('owner.dashboard')
            ->with('status', 'Your changes have been resubmitted for review.');
    }

    public function editBusiness(Business $business): View|RedirectResponse
    {
        Gate::authorize('update', $business);

        $pendingUpdate = $business->owner
            ->submissions()
            ->where('status', 'pending')
            ->where('business_data->business_id', $business->id)
            ->latest('created_at')
            ->first();

        if ($pendingUpdate !== null) {
            return redirect()->route('owner.submissions.edit', $pendingUpdate);
        }

        $business->load(['hours', 'images']);

        return view('owner.business-form', $this->formOptions(
            business: $business,
            values: $this->businessFormData($business),
        ));
    }

    public function submitBusinessUpdate(
        SubmitBusinessUpdateRequest $request,
        Business $business,
    ): RedirectResponse {
        $pendingUpdateExists = $business->owner
            ->submissions()
            ->where('status', 'pending')
            ->where('business_data->business_id', $business->id)
            ->exists();

        if ($pendingUpdateExists) {
            throw ValidationException::withMessages([
                'business' => 'An update for this business is already awaiting review.',
            ]);
        }

        $business->loadMissing('images');
        $existingImages = $business->images
            ->map(fn ($image): array => [
                'type' => $image->type,
                'path' => $image->path,
            ])
            ->all();

        [$businessData] = $this->submissionData(
            $request->validated(),
            $request->user()->id,
            $existingImages,
            $business->id,
        );

        $this->persistWithImageCleanup(
            fn (): Submission => $request->user()->submissions()->create([
                'business_data' => $businessData,
                'status' => 'pending',
            ]),
            $this->newImagePaths($businessData, $existingImages),
        );

        return redirect()
            ->route('owner.dashboard')
            ->with('status', 'Your listing changes have been submitted for review.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(?Submission $submission = null, ?Business $business = null, array $values = []): array
    {
        return [
            'submission' => $submission,
            'business' => $business,
            'values' => $values,
            'categories' => Category::query()->orderBy('name')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  list<array{type: string, path: string}>  $existingImages
     * @return array{0: array<string, mixed>, 1: list<string>}
     */
    private function submissionData(
        array $validated,
        int $ownerId,
        array $existingImages = [],
        ?int $businessId = null,
    ): array {
        $category = Category::query()->findOrFail($validated['category_id']);
        $location = Location::query()->findOrFail($validated['location_id']);
        $galleryUploads = $validated['gallery'] ?? [];
        $existingGalleryCount = collect($existingImages)->where('type', 'gallery')->count();

        if ($existingGalleryCount + count($galleryUploads) > 6) {
            throw ValidationException::withMessages([
                'gallery' => 'A business can have no more than six gallery images.',
            ]);
        }

        $images = collect($existingImages);
        $removedImages = [];
        $storedImagePaths = [];

        try {
            foreach (['logo', 'cover'] as $imageType) {
                if (! ($validated[$imageType] ?? null) instanceof UploadedFile) {
                    continue;
                }

                $previousImages = $images->where('type', $imageType);
                $removedImages = [
                    ...$removedImages,
                    ...$previousImages->pluck('path')->all(),
                ];
                $images = $images->reject(fn (array $image): bool => $image['type'] === $imageType);
                $path = $this->storeImage($validated[$imageType], $ownerId);
                $storedImagePaths[] = $path;
                $images->push([
                    'type' => $imageType,
                    'path' => $path,
                ]);
            }

            foreach ($galleryUploads as $galleryUpload) {
                $path = $this->storeImage($galleryUpload, $ownerId);
                $storedImagePaths[] = $path;
                $images->push([
                    'type' => 'gallery',
                    'path' => $path,
                ]);
            }
        } catch (Throwable $exception) {
            $this->deleteStoredImages($storedImagePaths);

            throw $exception;
        }

        unset($validated['logo'], $validated['cover'], $validated['gallery']);

        return [[
            ...$validated,
            'business_id' => $businessId,
            'category_name' => $category->name,
            'location_name' => $location->name,
            'images' => $images->values()->all(),
            'hours' => collect($validated['hours'])
                ->sortBy('day_of_week')
                ->values()
                ->map(fn (array $hour): array => [
                    'day_of_week' => (int) $hour['day_of_week'],
                    'is_closed' => (bool) $hour['is_closed'],
                    'open_time' => $hour['is_closed'] ? null : $hour['open_time'],
                    'close_time' => $hour['is_closed'] ? null : $hour['close_time'],
                ])
                ->all(),
        ], $removedImages];
    }

    private function storeImage(UploadedFile $image, int $ownerId): string
    {
        $path = $image->store("business-submissions/{$ownerId}", 'local');

        if ($path === false) {
            throw new \RuntimeException('The listing image could not be saved.');
        }

        return $path;
    }

    /**
     * @param  list<string>  $newImagePaths
     */
    private function persistWithImageCleanup(Closure $persist, array $newImagePaths): mixed
    {
        try {
            $result = $persist();

            if ($result === false) {
                throw new \RuntimeException('The business submission could not be saved.');
            }

            return $result;
        } catch (Throwable $exception) {
            $this->deleteStoredImages($newImagePaths);

            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>  $businessData
     * @param  list<array{type: string, path: string}>  $existingImages
     * @return list<string>
     */
    private function newImagePaths(array $businessData, array $existingImages = []): array
    {
        return collect(data_get($businessData, 'images', []))
            ->pluck('path')
            ->diff(collect($existingImages)->pluck('path'))
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $paths
     */
    private function deleteStoredImages(array $paths): void
    {
        if ($paths !== [] && ! Storage::disk('local')->delete($paths)) {
            Log::warning('One or more private business submission images could not be deleted.', [
                'paths' => $paths,
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function businessFormData(Business $business): array
    {
        $hours = $business->hours
            ->keyBy('day_of_week')
            ->map(fn ($hour): array => [
                'day_of_week' => (int) $hour->day_of_week,
                'is_closed' => $hour->is_closed,
                'open_time' => $hour->open_time,
                'close_time' => $hour->close_time,
            ]);

        return [
            'name' => $business->name,
            'category_id' => $business->category_id,
            'location_id' => $business->location_id,
            'address' => $business->address,
            'description' => $business->description,
            'services' => '',
            'phone' => $business->phone,
            'whatsapp' => '',
            'email' => $business->email,
            'website' => $business->website,
            'hours' => collect(range(0, 6))
                ->map(fn (int $day): array => $hours->get($day, [
                    'day_of_week' => $day,
                    'is_closed' => true,
                    'open_time' => null,
                    'close_time' => null,
                ]))
                ->all(),
            'images' => $business->images
                ->map(fn ($image): array => [
                    'type' => $image->type,
                    'path' => $image->path,
                ])
                ->all(),
        ];
    }
}
