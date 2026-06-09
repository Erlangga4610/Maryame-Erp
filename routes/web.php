<?php

use App\Auth\Livewire\Login;
use App\Content\Livewire\ContentCalendar;
use App\Dashboard\Livewire\Dashboard;
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
});
