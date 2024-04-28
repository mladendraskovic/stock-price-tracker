<?php

namespace App\Providers;

use App\Contracts\StockApi;
use App\Services\AlphaVantageApi;
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
        $this->app->bind(StockApi::class, AlphaVantageApi::class);
    }
}
