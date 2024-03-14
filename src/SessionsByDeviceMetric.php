<?php

namespace Tightenco\NovaGoogleAnalytics;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

class SessionsByDeviceMetric extends Partition
{
    public function name(): string
    {
        return __('Sessions by Device');
    }

    public function calculate(Request $request): PartitionResult
    {
        $analyticsData = Analytics::get(
            Period::months(1),
            ['sessions'],
            ['deviceCategory']
        );

        $results = collect($analyticsData)->flatMap(fn ($data) => [
            $data['deviceCategory'] ?? 'none' => $data['sessions'] ?? 0,
        ])->toArray();

        return $this->result($results)->label(fn ($label) => ucfirst($label));
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
