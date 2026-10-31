<?php

use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/directory', [DirectoryController::class, 'index'])->name('directory.index');
Route::get('/directory/{town}/{category}', [DirectoryController::class, 'landing'])
    ->name('directory.landing');
Route::get('/businesses/{slug}/reviews', [DirectoryController::class, 'reviews'])
    ->name('directory.reviews');
Route::get('/businesses/{slug}', [DirectoryController::class, 'show'])
    ->name('directory.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
