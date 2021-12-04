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
    use MetricDiffTrait;

    public function name(): string
    {
        return __('Page Views');
    }

    public function calculate(Request $request): ValueResult
    {
        $lookups = [
            1 => $this->pageViewsToday(),
            'Y' => $this->pageViewsYesterday(),
            'LW' =>$this->pageViewsLastWeek(),
            'LM' => $this->pageViewsLastMonth(),
            7 => $this->pageViewsLastSevenDays(),
            30 => $this->pageViewsLastThirtyDays(),
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
            ->previous($data['previous'])
            ->format('0,0');
    }

    private function pageViewsToday(): array
    {
        $analyticsData = app(Analytics::class)
            ->fetchTotalVisitorsAndPageViews(Period::days(1));

        return [
            'result' => $analyticsData->last()['pageViews'] ?? 0,
            'previous' => $analyticsData->first()['pageViews'] ?? 0,
        ];
    }

    private function pageViewsYesterday(): array
    {
        $analyticsData = app(Analytics::class)
            ->fetchTotalVisitorsAndPageViews(Period::create(Carbon::yesterday()->clone()->subDay(), Carbon::yesterday()));

        return [
            'result' => $analyticsData->last()['pageViews'] ?? 0,
            'previous' => $analyticsData->first()['pageViews'] ?? 0,
        ];
    }

    private function pageViewsLastWeek(): array
    {
        $lastWeek = $this->getLastWeek();
        $currentResults = $this->performQuery('ga:pageviews', 'ga:year', $lastWeek['current']);
        $previousResults = $this->performQuery('ga:pageviews', 'ga:year', $lastWeek['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function pageViewsLastMonth(): array
    {
        $lastMonth = $this->getLastMonth();
        $currentResults = $this->performQuery('ga:pageviews', 'ga:year', $lastMonth['current']);
        $previousResults = $this->performQuery('ga:pageviews', 'ga:year', $lastMonth['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function pageViewsLastSevenDays(): array
    {
        $lastSevenDays = $this->getLastSevenDays();
        $currentResults = $this->performQuery('ga:pageviews', 'ga:year', $lastSevenDays['current']);
        $previousResults = $this->performQuery('ga:pageviews', 'ga:year', $lastSevenDays['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function pageViewsLastThirtyDays(): array
    {
        $lastThirtyDays = $this->getLastThirtyDays();
        $currentResults = $this->performQuery('ga:pageviews', 'ga:year', $lastThirtyDays['current']);
        $previousResults = $this->performQuery('ga:pageviews', 'ga:year', $lastThirtyDays['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    public function ranges(): array
    {
        return [
            1 => __('Today'),
            'Y' => __('Yesterday'),
            'LW' => __('Last Week'),
            'LM' => __('Last Month'),
            7 => __('Last 7 Days'),
            30 => __('Last 30 Days'),
        ];
    }

    public function cacheFor(): \DateTime
    {
        return now()->addMinutes(30);
    }

    public function uriKey(): string
    {
        return 'page-views';
    }
}
