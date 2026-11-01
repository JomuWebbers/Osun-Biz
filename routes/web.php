<?php

use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Owner\BusinessSubmissionController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/directory', [DirectoryController::class, 'index'])->name('directory.index');
Route::get('/directory/{town}/{category}', [DirectoryController::class, 'landing'])
    ->name('directory.landing');
Route::get('/businesses/{slug}/reviews', [DirectoryController::class, 'reviews'])
    ->name('directory.reviews');
Route::get('/businesses/{slug}', [DirectoryController::class, 'show'])
    ->name('directory.show');

Route::get('/dashboard', function (Request $request) {
    return $request->user()->role === 'owner'
        ? redirect()->route('owner.dashboard')
        : redirect()->route('home');
})->middleware('auth')->name('dashboard');

Route::prefix('owner')
    ->name('owner.')
    ->middleware(['auth', 'verified', 'can:access-owner-dashboard'])
    ->group(function (): void {
        Route::get('/dashboard', OwnerDashboardController::class)->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');

        Route::get('/businesses/create', [BusinessSubmissionController::class, 'create'])
            ->name('businesses.create');
        Route::post('/businesses', [BusinessSubmissionController::class, 'store'])
            ->name('businesses.store');
        Route::get('/businesses/{business}/edit', [BusinessSubmissionController::class, 'editBusiness'])
            ->name('businesses.edit');
        Route::put('/businesses/{business}', [BusinessSubmissionController::class, 'submitBusinessUpdate'])
            ->name('businesses.update');

        Route::get('/submissions/{submission}/edit', [BusinessSubmissionController::class, 'editSubmission'])
            ->name('submissions.edit');
        Route::put('/submissions/{submission}', [BusinessSubmissionController::class, 'updateSubmission'])
            ->name('submissions.update');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
