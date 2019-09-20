<?php

namespace Tightenco\NovaGoogleAnalytics;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Value;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class VisitorsMetric extends Value
{
    public $name = 'Visitors';

    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        $lookups = [
            1 => $this->visitorsOneDay(),
            'MTD' => $this->visitorsOneMonth(),
            'YTD' => $this->visitorsOneYear(),
        ];

        $data = Arr::get($lookups, $request->get('range'), ['result' => 0, 'previous' => 0]);

        return $this
            ->result($data['result'])
            ->previous($data['previous']);
    }

    private function visitorsOneDay()
    {
        $analyticsData = app(Analytics::class)
            ->fetchTotalVisitorsAndPageViews(Period::days(1));

        $yesterday = $analyticsData->first();
        $today = $analyticsData->last();

        return [
            'result' => $analyticsData->last()['visitors'],
            'previous' => $analyticsData->first()['visitors'],
        ];
    }

    private function visitorsOneMonth()
    {
        $analyticsData = app(Analytics::class)->performQuery(
            Period::months(1),
            'ga:users',
            [
                'metrics' => 'ga:users',
                'dimensions' => 'ga:yearMonth',
            ]
        );

        $results = collect($analyticsData->getRows());

        return [
            'previous' => $results->first()[1],
            'result' => $results->last()[1],
        ];
    }

    private function visitorsOneYear()
    {
        $analyticsData = app(Analytics::class)->performQuery(
            Period::years(1),
            'ga:users',
            [
                'metrics' => 'ga:users',
                'dimensions' => 'ga:year',
            ]
        );

        $results = collect($analyticsData->getRows());

        return [
            'previous' => $results->first()[1],
            'result' => $results->last()[1],
        ];
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            1 => 'Today',
            // 30 => '30 Days',
            // 60 => '60 Days',
            // 365 => '365 Days',
            'MTD' => 'This month (to date)',
            // 'QTD' => 'Quarter To Date',
            'YTD' => 'This year (to date)',
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(30);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'visitors';
    }
}
