<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Metrics\Value;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class SessionsMetric extends Value
{
    use MetricDiffTrait;

    public function name()
    {
        return __('Sessions');
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
            1 => $this->sessionsOneDay(),
            'MTD' => $this->sessionsOneMonth(),
            'YTD' => $this->sessionsOneYear(),
        ];

        $data = Arr::get($lookups, $request->get('range'), ['result' => 0, 'previous' => 0]);

        return $this
            ->result($data['result'])
            ->previous($data['previous']);
    }

    private function sessionsOneDay()
    {
        $analyticsData = app(Analytics::class)
            ->performQuery(
                Period::days(1),
                'ga:sessions',
                [
                    'metrics' => 'ga:sessions',
                    'dimensions' => 'ga:date',
                ]
            );

        $results = collect($analyticsData->getRows());

        return [
            'result' => $results->last()[1] ?? 0,
            'previous' => $results->first()[1] ?? 0,
        ];
    }

    private function sessionsOneMonth()
    {
        $currentPeriod = Period::create(Carbon::today()->startOfMonth(), Carbon::today());
        $currentResults = $this->performQuery('ga:sessions', 'ga:yearMonth', $currentPeriod);

        [$start, $end] = $this->getPeriodDiff(Carbon::today()->startOfMonth());
        $previousPeriod = Period::create($start, $end);
        $previousResults = $this->performQuery('ga:sessions', 'ga:yearMonth', $previousPeriod);

        return [
            'previous' => $previousResults,
            'result' => $currentResults,
        ];
    }

    private function sessionsOneYear()
    {
        $currentPeriod = Period::create(Carbon::today()->startOfYear(), Carbon::today());
        $currentResults = $this->performQuery('ga:sessions', 'ga:year', $currentPeriod);

        [$start, $end] = $this->getPeriodDiff(Carbon::today()->startOfYear());
        $previousPeriod = Period::create($start, $end);
        $previousResults = $this->performQuery('ga:sessions', 'ga:year', $previousPeriod);

        return [
            'previous' => $previousResults,
            'result' => $currentResults,
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
            'YTD' => __('Year To Date'),
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
        return 'sessions';
    }
}
