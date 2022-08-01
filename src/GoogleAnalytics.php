<?php

namespace Tightenco\NovaGoogleAnalytics;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
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
                ->path('/nova-google-analytics/' . $property['id']);
        });
    }

    public function getClientForProperty(string $propertyID): ?BetaAnalyticsDataClient
    {
        $property = Arr::first(config('google-analytics.properties'), function ($property) use ($propertyID) {
            return $property['id'] == $propertyID;
        });

        if (is_null($property['gate']) || Gate::allows($property['gate'])) {
            return new BetaAnalyticsDataClient(['credentials' => $property['credentials']]);
        }

        return null;
    }
}
