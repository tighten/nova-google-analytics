<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;

trait MetricDiffTrait
{
    private function getPeriodDiff($startDate) {
        $currentPeriodDiff = $startDate->diffInDays(Carbon::today());
        $end = Carbon::yesterday()->subDays($currentPeriodDiff);
        $start = Carbon::yesterday()->subDays($currentPeriodDiff)->subDays($currentPeriodDiff);

        return [$start, $end];
    }
}
