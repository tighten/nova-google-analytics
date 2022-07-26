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
            __DIR__ . '/../config/google-analytics.php' => config_path('google-analytics.php'),
        ]);
    }
}
