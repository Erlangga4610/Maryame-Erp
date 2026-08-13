<?php

use App\Http\Controllers\GcsProxyController;
use App\Livewire\ApprovalPipeline\ApprovalWorkflow;
use App\Livewire\ApprovalPipeline\TikTokQcManager;
use App\Livewire\AssetManagement\AssetManager as AssetManagerComponent;
use App\Livewire\Auth\Login;
use App\Livewire\Content\ApprovalInbox;
use App\Livewire\Content\CalendarManagement;
use App\Livewire\Content\Capacity as CapacityPlanning;
use App\Livewire\Content\ContentCalendar;
use App\Livewire\Content\MixTracker;
use App\Livewire\Content\MyTasks;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\MasterData\Campaigns;
use App\Livewire\MasterData\Platforms;
use App\Livewire\MasterData\Products;
use App\Livewire\MasterData\Users;
use App\Livewire\Production\ProductionSchedule;
use App\Livewire\Profile;
use App\Livewire\PublishingReporting\AdjustmentManager;
use App\Livewire\PublishingReporting\PublishingManager;
use App\Livewire\RoleGuide;
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

    Route::get('/profile', Profile::class)->name('profile');

    Route::get('/contents', ContentCalendar::class)->name('contents.index');

    Route::get('/my-tasks', MyTasks::class)->name('my-tasks');

    Route::get('/calendar', CalendarManagement::class)->name('calendar');

    Route::get('/approval-inbox', ApprovalInbox::class)->name('approval.inbox');

    Route::get('/mix-tracker', MixTracker::class)->name('mix-tracker');

    Route::get('/production-schedule', ProductionSchedule::class)->name('production.schedule');

    Route::get('/capacity', CapacityPlanning::class)->name('capacity');

    Route::get('/qc/{contentId}', TikTokQcManager::class)->name('qc.manager');
    Route::get('/approval/{contentId}/{stage}', ApprovalWorkflow::class)->name('approval.workflow');
    Route::get('/publish/{contentId}', PublishingManager::class)->name('publishing.manager');
    Route::get('/adjustment/{adjustmentId}', AdjustmentManager::class)->name('adjustment.manager');
    Route::get('/assets/{contentId}', AssetManagerComponent::class)->name('assets.manager');

    Route::get('/storage/gcs/{path}', GcsProxyController::class)
        ->where('path', '.*')
        ->name('gcs.proxy');

    Route::get('/role-guide', RoleGuide::class)->name('role-guide');

    Route::get('/master-data/platforms', Platforms::class)->name('master-data.platforms');
    Route::get('/master-data/products', Products::class)->name('master-data.products');
    Route::get('/master-data/campaigns', Campaigns::class)->name('master-data.campaigns');
    Route::get('/master-data/users', Users::class)->name('master-data.users');
});
