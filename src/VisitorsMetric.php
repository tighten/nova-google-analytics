<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

class VisitorsMetric extends Value
{
    use MetricDiffTrait;

    public function name(): string
    {
        return __('Visitors');
    }

    public function calculate(Request $request): ValueResult
    {
        $lookups = [
            1 => $this->visitorsToday(),
            'Y' => $this->visitorsYesterday(),
            'LW' =>$this->visitorsLastWeek(),
            'LM' => $this->visitorsLastMonth(),
            7 => $this->visitorsLastSevenDays(),
            30 => $this->visitorsLastThirtyDays(),
        ];

        $data = Arr::get(
            $lookups,
            $request->get('range'),
            [
                'result' => 0,
                'previous' => 0,
            ]
        );

        return $this
            ->result($data['result'])
            ->previous($data['previous']);
    }

    private function visitorsToday(): array
    {
        $analyticsData = Analytics::fetchTotalVisitorsAndPageViews(Period::days(1));

        return [
            'result' => $analyticsData->last()['visitors'] ?? 0,
            'previous' => $analyticsData->first()['visitors'] ?? 0,
        ];
    }

    private function visitorsYesterday(): array
    {
        $analyticsData = Analytics::fetchTotalVisitorsAndPageViews(Period::create(Carbon::yesterday()->clone()->subDay(), Carbon::yesterday()));

        return [
            'result' => $analyticsData->last()['visitors'] ?? 0,
            'previous' => $analyticsData->first()['visitors'] ?? 0,
        ];
    }

    private function visitorsLastWeek(): array
    {
        $lastWeek = $this->getLastWeek();
        $currentResults = $this->performQuery('totalUsers', 'date', $lastWeek['current']);
        $previousResults = $this->performQuery('totalUsers', 'date', $lastWeek['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function visitorsLastMonth(): array
    {
        $lastMonth = $this->getLastMonth();
        $currentResults = $this->performQuery('totalUsers', 'year', $lastMonth['current']);
        $previousResults = $this->performQuery('totalUsers', 'year', $lastMonth['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function visitorsLastSevenDays(): array
    {
        $lastSevenDays = $this->getLastSevenDays();
        $currentResults = $this->performQuery('totalUsers', 'year', $lastSevenDays['current']);
        $previousResults = $this->performQuery('totalUsers', 'year', $lastSevenDays['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function visitorsLastThirtyDays(): array
    {
        $lastThirtyDays = $this->getLastThirtyDays();
        $currentResults = $this->performQuery('totalUsers', 'year', $lastThirtyDays['current']);
        $previousResults = $this->performQuery('totalUsers', 'year', $lastThirtyDays['previous']);

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
        return 'visitors';
    }
}
