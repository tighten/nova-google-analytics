<?php

namespace Tightenco\NovaGoogleAnalytics;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class SessionsByDeviceMetric extends Partition
{
    public function name(): string
    {
        return __('Sessions by Device');
    }

    public function calculate(Request $request): PartitionResult
    {
        $analyticsData = app(Analytics::class)
            ->performQuery(
                Period::months(1),
                'ga:sessions',
                [
                    'metrics' => 'ga:sessions',
                    'dimensions' => 'ga:deviceCategory',
                ]
            );

        $rows = collect($analyticsData->getRows());

        $results = [];
        foreach ($rows as $row) {
            $results[$row[0]] = $row[1];
        }

        return $this
            ->result($results)
            ->label(function ($value) {
                switch ($value) {
                    case null:
                        return 'None';
                    default:
                        return ucfirst($value);
                }
            });
    }

    public function cacheFor(): \DateTime
    {
        return now()->addMinutes(30);
    }

    public function uriKey(): string
    {
        return 'sessions-by-device';
    }
}
