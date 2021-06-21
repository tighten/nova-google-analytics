<?php

namespace Tightenco\NovaGoogleAnalytics;

use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;
use Carbon\Carbon;

trait ActiveUsersTrait
{
    private function performQuery($metric, $days)
    {
        $analyticsData = app(Analytics::class)
            ->performQuery(
                Period::days($days),
                $metric,
                [
                    'metrics' => $metric,
                    'dimensions' => 'ga:date',
                ]
            );

        $rows = collect($analyticsData->getRows());

        $results = [];
        foreach ($rows as $row) {
            $date = new Carbon($row[0]);
            $results[$date->format('M j')] = intval($row[1]);
        }

        return ['results' => $results];
    }
}