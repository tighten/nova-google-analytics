<?php

namespace Tightenco\NovaGoogleAnalytics;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Menu\MenuItem;

class GoogleAnalytics
{
    public function getProperties(): array
    {
        return Arr::where(config('google-analytics.properties'), function ($property) {
            return is_null($property['gate']) || Gate::allows($property['gate']);
        });
    }

    public function getPropertyMenu(): array
    {
        return Arr::map($this->getProperties(), function ($property) {
            return MenuItem::make($property['name'])
                ->path('/nova-google-analytics');
        });
    }
}
