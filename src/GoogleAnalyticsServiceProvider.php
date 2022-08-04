<?php

namespace Tightenco\NovaGoogleAnalytics;

use Illuminate\Support\ServiceProvider;

class GoogleAnalyticsServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../stubs/app/Models/AnalyticsProject.php' => app_path('Models/AnalyticsProject.php'),
            __DIR__ . '/../stubs/app/Nova/AnalyticsProject.php' => app_path('Nova/AnalyticsProject.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
