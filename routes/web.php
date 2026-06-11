<?php

use App\Auth\Livewire\Login;
use App\Content\Livewire\ApprovalInbox;
use App\Content\Livewire\ContentCalendar;
use App\Dashboard\Livewire\Dashboard;
use App\MasterData\Livewire\Campaigns;
use App\MasterData\Livewire\Platforms;
use App\MasterData\Livewire\Products;
use App\MasterData\Livewire\Users;
use App\Production\Livewire\ProductionSchedule;
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

    Route::get('/contents', ContentCalendar::class)->name('contents.index');

    Route::get('/approval-inbox', ApprovalInbox::class)->name('approval.inbox');

    Route::get('/production-schedule', ProductionSchedule::class)->name('production.schedule');

    Route::get('/master-data/platforms', Platforms::class)->name('master-data.platforms');
    Route::get('/master-data/products', Products::class)->name('master-data.products');
    Route::get('/master-data/campaigns', Campaigns::class)->name('master-data.campaigns');
    Route::get('/master-data/users', Users::class)->name('master-data.users');
});
