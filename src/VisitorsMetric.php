<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class VisitorsMetric extends Value
{
    public function name()
    {
        return __('Visitors');
    }

    public function calculate(Request $request): ValueResult
    {
        $lookups = [
            1 => $this->visitorsOneDay(),
            'MTD' => $this->visitorsOneMonth(),
            'YTD' => $this->visitorsOneYear(),
        ];

        $data = Arr::get($lookups, $request->get('range'), ['result' => 0, 'previous' => 0]);

        return $this->result($data['result'])
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

    public function cacheFor()
    {
        return now()->addMinutes(30);
    }

    public function uriKey(): string
    {
        return 'visitors';
    }

    private function visitorsOneDay(): array
    {
        $analyticsData = app(Analytics::class)
            ->fetchTotalVisitorsAndPageViews(Period::days(1));

        return [
            'result' => $analyticsData->last()['visitors'] ?? 0,
            'previous' => $analyticsData->first()['visitors'] ?? 0,
        ];
    }

    private function visitorsOneMonth(): array
    {
        $currentAnalyticsData = app(Analytics::class)->performQuery(
            Period::create(Carbon::today()->startOfMonth(), Carbon::today()),
            'ga:users',
            [
                'metrics' => 'ga:users',
                'dimensions' => 'ga:yearMonth',
            ]
        );
        $currentResults = collect($currentAnalyticsData->getRows());

        $lastMonth = Carbon::today()->startOfMonth()->subMonths(1);
        $previousAnalyticsData = app(Analytics::class)->performQuery(
            Period::create($lastMonth->startOfMonth(), $lastMonth->endOfMonth()),
            'ga:users',
            [
                'metrics' => 'ga:users',
                'dimensions' => 'ga:yearMonth',
            ]
        );
        $previousResults = collect($previousAnalyticsData->getRows());

        return [
            'previous' => $previousResults->last()[1] ?? 0,
            'result' => $currentResults->last()[1] ?? 0,
        ];
    }

    private function visitorsOneYear(): array
    {
        $currentAnalyticsData = app(Analytics::class)->performQuery(
            Period::create(Carbon::today()->startOfYear(), Carbon::today()),
            'ga:users',
            [
                'metrics' => 'ga:users',
                'dimensions' => 'ga:year',
            ]
        );
        $currentResults = collect($currentAnalyticsData->getRows());

        $lastYear = Carbon::today()->startOfYear()->subYears(1);
        $previousAnalyticsData = app(Analytics::class)->performQuery(
            Period::create($lastYear->startOfYear(), $lastYear->endOfYear()),
            'ga:users',
            [
                'metrics' => 'ga:users',
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
