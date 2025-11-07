<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Increase memory limit for admin operations
        if (request()->is('admin/*') || request()->is('livewire/*')) {
            @ini_set('memory_limit', '512M');
            @ini_set('max_execution_time', '300');
        }
    }
}
