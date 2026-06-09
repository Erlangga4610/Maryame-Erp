<?php

namespace App\Providers;

use App\Content\Models\Content;
use App\Content\Observers\ContentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Content::observe(ContentObserver::class);
    }
}
