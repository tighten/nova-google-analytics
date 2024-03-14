<?php

namespace Tightenco\NovaGoogleAnalytics;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\OrderBy;
use Spatie\Analytics\Period;

class SessionsByCountryMetric extends Partition
{
    public function name(): string
    {
        return __('Sessions by Country - Top 5');
    }

    public function calculate(Request $request): PartitionResult
    {
        $analyticsData = Analytics::get(
            Period::months(1),
            ['sessions'],
            ['country'],
            5,
            [OrderBy::metric('sessions', true)],
        );

        $results = collect($analyticsData)->flatMap(fn ($data) => [
            $data['country'] ?? 'None' => $data['sessions'] ?? 0,
        ])->toArray();

        return $this->result($results);
    }

    public function cacheFor(): \DateTime
    {
        return now()->addMinutes(30);
    }

    public function uriKey(): string
    {
        return 'sessions-by-country';
    }
}
