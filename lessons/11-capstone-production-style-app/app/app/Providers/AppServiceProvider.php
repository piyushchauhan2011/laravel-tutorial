<?php

namespace App\Providers;

use App\Support\CapstoneFeatures;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;

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
        Paginator::useBootstrapFive();

        foreach (CapstoneFeatures::all() as $feature) {
            Feature::define($feature, fn (): bool => CapstoneFeatures::defaultValue($feature));
        }
    }
}
