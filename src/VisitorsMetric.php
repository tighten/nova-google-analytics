<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Value;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class VisitorsMetric extends Value
{
    public $name = 'GA Visitors Today';

    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        return $this
            ->result($this->visitorsForToday())
            ->previous($this->visitorsForYesterday());
    }

    private function visitorsForYesterday()
    {
        $analyticsData = app(Analytics::class)
            ->fetchTotalVisitorsAndPageViews(Period::days(1));

        $yesterday = $analyticsData->first();
        $today = $analyticsData->last();

        return $yesterday['visitors'];
    }

    private function visitorsForToday()
    {
        $analyticsData = app(Analytics::class)
            ->fetchTotalVisitorsAndPageViews(Period::days(1));

        $yesterday = $analyticsData->first();
        $today = $analyticsData->last();

        return $today['visitors'];
    }

    /* @todo for older ranges:
        $analyticsData = app(Analytics::class)->performQuery(
            Period::months(2),
            'ga:users',
            [
                // 'metrics' => 'ga:sessions, ga:pageviews',
                'metrics' => 'ga:users',
                'dimensions' => 'ga:yearMonth'
            ]
        );
        */

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            // 1 => '1 day',
            // 30 => '30 Days',
            // 60 => '60 Days',
            // 365 => '365 Days',
            // 'MTD' => 'Month To Date',
            // 'QTD' => 'Quarter To Date',
            // 'YTD' => 'Year To Date',
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
        return 'visitors';
    }
}
