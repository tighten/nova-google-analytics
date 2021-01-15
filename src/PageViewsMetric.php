<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Metrics\Value;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class PageViewsMetric extends Value
{
    public $name = 'Page Views';

    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        $lookups = [
            1 => $this->pageViewsOneDay(),
            'MTD' => $this->pageViewsOneMonth(),
            'YTD' => $this->pageViewsOneYear(),
        ];

        $data = Arr::get($lookups, $request->get('range'), ['result' => 0, 'previous' => 0]);

        return $this
            ->result($data['result'])
            ->previous($data['previous']);
    }

    private function pageViewsOneDay()
    {
        $analyticsData = app(Analytics::class)
            ->fetchTotalVisitorsAndPageViews(Period::days(1));

        return [
            'previous' => $analyticsData->first()['pageViews'],
            'result' => $analyticsData->last()['pageViews'],
        ];
    }

    private function pageViewsOneMonth()
    {
        $currentAnalyticsData = app(Analytics::class)->performQuery(
            Period::create(Carbon::today()->startOfMonth(), Carbon::today()),
            'ga:pageviews',
            [
                'metrics' => 'ga:pageviews',
                'dimensions' => 'ga:yearMonth',
            ]
        );

        $currentResults = collect($currentAnalyticsData->getRows());

        $previousAnalyticsData = app(Analytics::class)->performQuery(
            Period::create(Carbon::today()->subMonths(1)->startOfMonth(), Carbon::today()->subMonths(1)->endOfMonth()),
            'ga:pageviews',
            [
                'metrics' => 'ga:pageviews',
                'dimensions' => 'ga:yearMonth',
            ]
        );

        $previousResults = collect($previousAnalyticsData->getRows());

        return [
            'previous' => $previousResults->last()[1],
            'result' => $currentResults->last()[1],
        ];
    }

    private function pageViewsOneYear()
    {
        $currentAnalyticsData = app(Analytics::class)->performQuery(
            Period::create(Carbon::today()->startOfYear(), Carbon::today()),
            'ga:pageviews',
            [
                'metrics' => 'ga:pageviews',
                'dimensions' => 'ga:year',
            ]
        );

        $currentResults = collect($currentAnalyticsData->getRows());

        $previousAnalyticsData = app(Analytics::class)->performQuery(
            Period::create(Carbon::today()->subYears(1)->startOfYear(), Carbon::today()->subYears(1)->endOfYear()),
            'ga:pageviews',
            [
                'metrics' => 'ga:pageviews',
                'dimensions' => 'ga:year',
            ]
        );

        $previousResults = collect($previousAnalyticsData->getRows());

        return [
            'previous' => $previousResults->last()[1],
            'result' => $currentResults->last()[1],
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
            'MTD' => 'This month (to date)',
            // 60 => '60 Days',
            'YTD' => 'This year (to date)',
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
        return 'page-views';
    }
}
