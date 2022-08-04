<?php

namespace Tightenco\NovaGoogleAnalytics;

use App\Models\AnalyticsProject;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Menu\MenuItem;

class GoogleAnalytics
{
    public function getProperties(): Collection
    {
        return AnalyticsProject::all()->filter(function ($property) {
            return is_null($property->gate) || Gate::allows($property->gate);
        });
    }

    public function getPropertyMenu(): Collection
    {
        return $this->getProperties()->map(function ($property) {
            return MenuItem::make($property->project_name)
                ->path('/nova-google-analytics/' . $property->project_id);
        });
    }

    public function getClientForProperty(string $propertyID): ?BetaAnalyticsDataClient
    {
        $property = AnalyticsProject::where('project_id', $propertyID)->first();

        if (is_null($property->gate) || Gate::allows($property->gate)) {
            return new BetaAnalyticsDataClient(['credentials' => storage_path('app/' . $property->credentials)]);
        }

        return null;
    }
}
