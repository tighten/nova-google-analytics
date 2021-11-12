<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Metrics\Value;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class VisitorsMetric extends Value
{
    use MetricDiffTrait;

    public function name()
    {
        return __('Visitors');
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
            1 => $this->visitorsToday(),
            'Y' => $this->visitorsYesterday(),
            'LW' =>$this->visitorsLastWeek(),
            'LM' => $this->visitorsLastMonth(),
            7 => $this->visitorsLastSevenDays(),
            30 => $this->visitorsLastThirtyDays(),
        ];

        $data = Arr::get($lookups, $request->get('range'), ['result' => 0, 'previous' => 0]);

        return $this->result($data['result'])
                    ->previous($data['previous']);
    }

    private function visitorsToday(): array
    {
        $analyticsData = app(Analytics::class)
            ->fetchTotalVisitorsAndPageViews(Period::days(1));

        return [
            'result' => $analyticsData->last()['visitors'] ?? 0,
            'previous' => $analyticsData->first()['visitors'] ?? 0,
        ];
    }

    private function visitorsYesterday(): array
    {
        $analyticsData = app(Analytics::class)
            ->fetchTotalVisitorsAndPageViews(Period::create(Carbon::yesterday()->clone()->subDay(), Carbon::yesterday()));

        return [
            'result' => $analyticsData->last()['visitors'] ?? 0,
            'previous' => $analyticsData->first()['visitors'] ?? 0,
        ];
    }

    private function visitorsLastWeek(): array
    {
        $currentResults = $this->performQuery('ga:users', 'ga:date', $this->getLastWeek()['current']);
        $previousResults = $this->performQuery('ga:users', 'ga:date', $this->getLastWeek()['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum(),
            'result' => $currentResults->pluck('value')->sum(),
        ];
    }

    private function visitorsLastMonth(): array
    {
        $currentResults = $this->performQuery('ga:users', 'ga:date', $this->getLastMonth()['current']);
        $previousResults = $this->performQuery('ga:users', 'ga:date', $this->getLastMonth()['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum(),
            'result' => $currentResults->pluck('value')->sum(),
        ];
    }

    private function visitorsLastSevenDays(): array
    {
        $currentResults = $this->performQuery('ga:users', 'ga:date', $this->getLastSevenDays()['current']);
        $previousResults = $this->performQuery('ga:users', 'ga:date', $this->getLastSevenDays()['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum(),
            'result' => $currentResults->pluck('value')->sum(),
        ];
    }

    private function visitorsLastThirtyDays(): array
    {
        $currentResults = $this->performQuery('ga:users', 'ga:date', $this->getLastThirtyDays()['current']);
        $previousResults = $this->performQuery('ga:users', 'ga:date', $this->getLastThirtyDays()['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum(),
            'result' => $currentResults->pluck('value')->sum(),
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
            'Y' => __('Yesterday'),
            'LW' => __('Last Week'),
            'LM' => __('Last Month'),
            7 => __('Last 7 Days'),
            30 => __('Last 30 Days'),
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
