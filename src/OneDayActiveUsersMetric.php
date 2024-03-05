<?php

namespace Tightenco\NovaGoogleAnalytics;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class OneDayActiveUsersMetric extends Trend
{
    use ActiveUsersTrait;

    public function name(): string
    {
        return __('1 Day Active Users');
    }

    public function calculate(Request $request): TrendResult
    {
        $lookups = [
            5 => $this->performQuery('active1dayUsers', 5),
            10 => $this->performQuery('active1dayUsers', 10),
            15 => $this->performQuery('active1dayUsers', 15),
        ];

        $data = Arr::get($lookups, $request->get('range'), ['results' => [0]]);

        return (new TrendResult)->trend($data['results'])
            ->showLatestValue()
            ->format('0,0');
    }

    public function ranges(): array
    {
        return [
            5 => __('5 Days'),
            10 => __('10 Days'),
            15 => __('15 Days'),
        ];
    }

    public function cacheFor(): \DateTime
    {
        return now()->addMinutes(30);
    }

    public function uriKey(): string
    {
        return 'one-day-active-users';
    }
}
