<?php

use App\Livewire\Auth\Login;
use App\Livewire\Content\ApprovalInbox;
use App\Livewire\Content\MixTracker;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\ApprovalPipeline\TikTokQcManager;
use App\Livewire\ApprovalPipeline\ApprovalWorkflow;
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

    Route::get('/approval-inbox', ApprovalInbox::class)->name('approval.inbox');

    Route::get('/mix-tracker', MixTracker::class)->name('mix-tracker');

    Route::get('/qc/{contentId}', TikTokQcManager::class)->name('qc');

    Route::get('/approval/{contentId}/{stage}', ApprovalWorkflow::class)->name('approval');
});
