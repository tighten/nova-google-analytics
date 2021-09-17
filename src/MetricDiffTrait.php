<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

trait MetricDiffTrait
{
    private function getPeriodDiff($startDate) {
        $currentPeriodDiff = $startDate->diffInDays(Carbon::today());
        $end = Carbon::yesterday()->subDays($currentPeriodDiff);
        $start = Carbon::yesterday()->subDays($currentPeriodDiff)->subDays($currentPeriodDiff);

        return [$start, $end];
    }

    private function performQuery(string $metric, string $dimensions, Period $period): int
    {
        $analyticsData = app(Analytics::class)
            ->performQuery(
                $period,
                $metric,
                [
                    'metrics' => $metric,
                    'dimensions' => $dimensions,
                    'samplingLevel' => 'HIGHER_PRECISION',
                ]
            );

        $results = collect($analyticsData->getRows());

        return $results->last()[1] ?? 0;
    }
}
