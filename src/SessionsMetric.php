<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\ValueResult;
use Spatie\Analytics\Period;

class SessionsMetric extends Value
{
    use MetricDiffTrait;

    public function name(): string
    {
        return __('Sessions');
    }

    public function calculate(Request $request): ValueResult
    {
        $lookups = [
            1 => $this->sessionsToday(),
            'Y' => $this->sessionsYesterday(),
            'LW' =>$this->sessionsLastWeek(),
            'LM' => $this->sessionsLastMonth(),
            7 => $this->sessionsLastSevenDays(),
            30 => $this->sessionsLastThirtyDays(),
        ];

        $data = Arr::get($lookups, $request->get('range'), ['result' => 0, 'previous' => 0]);

        return $this
            ->result($data['result'])
            ->previous($data['previous']);
    }

    private function sessionsToday(): array
    {
        $results = $this->performQuery('sessions', 'date', Period::days(1));

        return [
            'previous' => $results->first()['value'] ?? 0,
            'result' => $results->last()['value'] ?? 0,
        ];
    }

    private function sessionsYesterday(): array
    {
        $results = $this->performQuery('sessions', 'date', Period::create(Carbon::yesterday()->clone()->subDay(), Carbon::yesterday()));

        return [
            'previous' => $results->first()['value'] ?? 0,
            'result' => $results->last()['value'] ?? 0,
        ];
    }

    private function sessionsLastWeek(): array
    {
        $lastWeek = $this->getLastWeek();
        $currentResults = $this->performQuery('sessions', 'year', $lastWeek['current']);
        $previousResults = $this->performQuery('sessions', 'year', $lastWeek['previous']);

        return [
            'previous' => $previousResults->pluck('value')[0] ?? 0,
            'result' => $currentResults->pluck('value')[0] ?? 0,
        ];
    }

    private function sessionsLastMonth(): array
    {
        $lastMonth = $this->getLastMonth();
        $currentResults = $this->performQuery('sessions', 'year', $lastMonth['current']);
        $previousResults = $this->performQuery('sessions', 'year', $lastMonth['previous']);

        return [
            'previous' => $previousResults->pluck('value')[0] ?? 0,
            'result' => $currentResults->pluck('value')[0] ?? 0,
        ];
    }

    private function sessionsLastSevenDays(): array
    {
        $lastSevenDays = $this->getLastSevenDays();
        $currentResults = $this->performQuery('sessions', 'year', $lastSevenDays['current']);
        $previousResults = $this->performQuery('sessions', 'year', $lastSevenDays['previous']);

        return [
            'previous' => $previousResults->pluck('value')[0] ?? 0,
            'result' => $currentResults->pluck('value')[0] ?? 0,
        ];
    }

    private function sessionsLastThirtyDays(): array
    {
        $lastThirtyDays = $this->getLastThirtyDays();
        $currentResults = $this->performQuery('sessions', 'year', $lastThirtyDays['current']);
        $previousResults = $this->performQuery('sessions', 'year', $lastThirtyDays['previous']);

        return [
            'previous' => $previousResults->pluck('value')[0] ?? 0,
            'result' => $currentResults->pluck('value')[0] ?? 0,
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
        return 'sessions';
    }
}
