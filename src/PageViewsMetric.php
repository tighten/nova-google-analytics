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
    public function name() {
        return __('Page Views');
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
            'result' => $analyticsData->first()['pageViews'] ?? 0,
            'previous' => $analyticsData->last()['pageViews'] ?? 0,
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

        $lastMonth = Carbon::today()->startOfMonth()->subMonths(1);
        $previousAnalyticsData = app(Analytics::class)->performQuery(
            Period::create($lastMonth->startOfMonth(), $lastMonth->endOfMonth()),
            'ga:pageviews',
            [
                'metrics' => 'ga:pageviews',
                'dimensions' => 'ga:yearMonth',
            ]
        );

        $previousResults = collect($previousAnalyticsData->getRows());

        return [
            'previous' => $previousResults->last()[1] ?? 0,
            'result' => $currentResults->last()[1] ?? 0,
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

        $lastYear = Carbon::today()->startOfYear()->subYears(1);
        $previousAnalyticsData = app(Analytics::class)->performQuery(
            Period::create($lastYear->startOfYear(), $lastYear->endOfYear()),
            'ga:pageviews',
            [
                'metrics' => 'ga:pageviews',
                'dimensions' => 'ga:year',
            ]
        );

        $previousResults = collect($previousAnalyticsData->getRows());

        return [
            'previous' => $previousResults->last()[1] ?? 0,
            'result' => $currentResults->last()[1] ?? 0,
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
            1 => __('Today'),
            'MTD' => __('Month To Date'),
            // 60 => '60 Days',
            'YTD' => __('Year To Date'),
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
