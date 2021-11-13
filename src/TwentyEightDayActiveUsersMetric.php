<?php

namespace Tightenco\NovaGoogleAnalytics;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class TwentyEightDayActiveUsersMetric extends Trend
{
    use ActiveUsersTrait;

    public function name(): string
    {
        return __('28 Day Active Users');
    }

    public function calculate(Request $request): TrendResult
    {
        $lookups = [
            5 => $this->performQuery('ga:28dayUsers', 5),
            10 => $this->performQuery('ga:28dayUsers', 10),
            15 => $this->performQuery('ga:28dayUsers', 15),
        ];

        $data = Arr::get($lookups, $request->get('range'), ['results' => [0]]);

        return (new TrendResult)->trend($data['results'])
            ->showLatestValue()
            ->format('0,0');
    }

    public function ranges(): array
    {
        return [
            5 => '5 Days',
            10 => '10 Days',
            15 => '15 Days',
        ];
    }

    public function cacheFor(): \DateTime
    {
        return now()->addMinutes(30);
    }

    public function uriKey(): string
    {
        return 'twenty-eight-day-active-users';
    }
}
