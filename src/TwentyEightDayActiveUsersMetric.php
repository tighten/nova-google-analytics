<?php

namespace Tightenco\NovaGoogleAnalytics;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;
use Carbon\Carbon;

class TwentyEightDayActiveUsersMetric extends Trend
{
    public function name()
    {
        return __('28 Day Active Users');
    }

    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        $lookups = [
            5 => $this->activeUsersFiveDays(),
            10 => $this->activeUsersTenDays(),
            15 => $this->activeUsersFifteenDays(),
        ];

        $data = Arr::get($lookups, $request->get('range'), ['results' => [0]]);

        return (new TrendResult)->trend($data['results'])
            ->showLatestValue()
            ->format('0,0');
    }

    private function activeUsersFiveDays()
    {
        $analyticsData = app(Analytics::class)
            ->performQuery(
                Period::days(5),
                'ga:1dayUsers',
                [
                    'metrics' => 'ga:28dayUsers',
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

    private function activeUsersTenDays()
    {
        $analyticsData = app(Analytics::class)
            ->performQuery(
                Period::days(10),
                'ga:1dayUsers',
                [
                    'metrics' => 'ga:28dayUsers',
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

    private function activeUsersFifteenDays()
    {
        $analyticsData = app(Analytics::class)
            ->performQuery(
                Period::days(15),
                'ga:1dayUsers',
                [
                    'metrics' => 'ga:28dayUsers',
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

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            5 => '5 Days',
            10 => '10 Days',
            15 => '15 Days',
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        return now()->addMinutes(30);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'twenty-eight-day-active-users';
    }
}
