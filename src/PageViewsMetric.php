<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class PageViewsMetric extends Value
{
    public function name(): string
    {
        return __('Page Views');
    }

    public function calculate(Request $request): ValueResult
    {
        $lookups = [
            1 => $this->pageViewsOneDay(),
            'MTD' => $this->pageViewsOneMonth(),
            'YTD' => $this->pageViewsOneYear(),
        ];

        $data = Arr::get(
            $lookups,
            $request->get('range'),
            [
                'result' => 0,
                'previous' => 0,
            ],
        );

        return $this
            ->result($data['result'])
            ->previous($data['previous']);
    }

    public function ranges(): array
    {
        return [
            1 => __('Today'),
            'MTD' => __('Month To Date'),
            'YTD' => __('Year To Date'),
        ];
    }

    public function cacheFor(): Carbon
    {
        return now()->addMinutes(30);
    }

    public function uriKey(): string
    {
        return 'page-views';
    }

    private function pageViewsOneDay(): array
    {
        $analyticsData = app(Analytics::class)
            ->fetchTotalVisitorsAndPageViews(Period::days(1));

        return [
            'result' => $analyticsData->last()['pageViews'] ?? 0,
            'previous' => $analyticsData->first()['pageViews'] ?? 0,
        ];
    }

    private function pageViewsOneMonth(): array
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

    private function pageViewsOneYear(): array
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
}
