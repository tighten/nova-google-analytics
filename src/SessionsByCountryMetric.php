<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class SessionsByCountryMetric extends Partition
{
    public function name(): string
    {
        return __('Sessions by Country - Top 5');
    }

    public function calculate(Request $request): PartitionResult
    {
        $analyticsData = app(Analytics::class)
            ->performQuery(
                Period::months(1),
                'ga:sessions',
                [
                    'metrics' => 'ga:sessions',
                    'dimensions' => 'ga:country',
                    'sort' => '-ga:sessions',
                    'max-results' => 5,
                ]
            );

        $rows = collect($analyticsData->getRows());

        $results = [];
        foreach ($rows as $row) {
            $results[$row[0]] = $row[1];
        }

        return $this->result($results);
    }

    public function cacheFor(): Carbon
    {
        return now()->addMinutes(30);
    }

    public function uriKey(): string
    {
        return 'sessions-by-country';
    }
}
