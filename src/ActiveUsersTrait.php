<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

trait ActiveUsersTrait
{
    private function performQuery(string $metric, int $days): array
    {
        $analyticsData = Analytics::get(
            Period::days($days),
            [$metric],
            ['date']
        );

        $results = $analyticsData->mapWithKeys(fn ($data) => [
            (new Carbon($data['date']))->format('M j') => intval($data[$metric]),
        ]);

        return ['results' => $results->toArray()];
    }
}
