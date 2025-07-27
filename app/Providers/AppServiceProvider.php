<?php

namespace App\Providers;

use App\Models\RecipeCalculation;
use App\Observers\RecipeCalculationObserver;
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
        RecipeCalculation::observe(RecipeCalculationObserver::class);
    }
}
