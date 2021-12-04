<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;
use Spatie\Analytics\Period;

class UniquePageViews extends Value
{
    use MetricDiffTrait;

    public function name(): string
    {
        return __('Unique Page Views');
    }

    public function calculate(Request $request): ValueResult
    {
        $lookups = [
            1 => $this->uniquePageViewsToday(),
            'Y' => $this->uniquePageViewsYesterday(),
            'LW' =>$this->uniquePageViewsLastWeek(),
            'LM' => $this->uniquePageViewsLastMonth(),
            7 => $this->uniquePageViewsLastSevenDays(),
            30 => $this->uniquePageViewsLastThirtyDays(),
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

    private function uniquePageViewsToday(): array
    {
        $analyticsData = $this->performQuery('ga:uniquePageviews', 'ga:date', Period::days(1));

        return [
            'result' => $analyticsData->last()['uniquePageviews'] ?? 0,
            'previous' => $analyticsData->first()['uniquePageviews'] ?? 0,
        ];
    }

    private function uniquePageViewsYesterday(): array
    {
        $analyticsData = $this->performQuery('ga:uniquePageviews', 'ga:date', Period::create(Carbon::yesterday()->clone()->subDay(), Carbon::yesterday()));

        return [
            'result' => $analyticsData->last()['uniquePageviews'] ?? 0,
            'previous' => $analyticsData->first()['uniquePageviews'] ?? 0,
        ];
    }

    private function uniquePageViewsLastWeek(): array
    {
        $lastWeek = $this->getLastWeek();
        $currentResults = $this->performQuery('ga:uniquePageviews', 'ga:year', $lastWeek['current']);
        $previousResults = $this->performQuery('ga:uniquePageviews', 'ga:year', $lastWeek['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function uniquePageViewsLastMonth(): array
    {
        $lastMonth = $this->getLastMonth();
        $currentResults = $this->performQuery('ga:uniquePageviews', 'ga:year', $lastMonth['current']);
        $previousResults = $this->performQuery('ga:uniquePageviews', 'ga:year', $lastMonth['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function uniquePageViewsLastSevenDays(): array
    {
        $lastSevenDays = $this->getLastSevenDays();
        $currentResults = $this->performQuery('ga:uniquePageviews', 'ga:year', $lastSevenDays['current']);
        $previousResults = $this->performQuery('ga:uniquePageviews', 'ga:year', $lastSevenDays['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function uniquePageViewsLastThirtyDays(): array
    {
        $lastThirtyDays = $this->getLastThirtyDays();
        $currentResults = $this->performQuery('ga:uniquePageviews', 'ga:year', $lastThirtyDays['current']);
        $previousResults = $this->performQuery('ga:uniquePageviews', 'ga:year', $lastThirtyDays['previous']);

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
        return 'unique-page-views';
    }
}
