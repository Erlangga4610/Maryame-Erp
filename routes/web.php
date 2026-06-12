<?php

use App\Livewire\Auth\Login;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\PublishingReporting\PublishingManager;
use App\Livewire\PublishingReporting\AdjustmentManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::get('/login', Login::class)->name('login');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/login');
    })->name('logout');

    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/publish/{contentId}', PublishingManager::class)->name('publish');

    Route::get('/adjustment/{adjustmentId}', AdjustmentManager::class)->name('adjustment');
});
