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
            1 => $this->visitorsOneDay(),
            'MTD' => $this->visitorsOneMonth(),
            'YTD' => $this->visitorsOneYear(),
        ];

        $data = Arr::get($lookups, $request->get('range'), ['result' => 0, 'previous' => 0]);

        return $this->result($data['result'])
                    ->previous($data['previous']);
    }

    private function visitorsOneDay()
    {
        $analyticsData = app(Analytics::class)
            ->fetchTotalVisitorsAndPageViews(Period::days(1));

        return [
            'result' => $analyticsData->last()['visitors'] ?? 0,
            'previous' => $analyticsData->first()['visitors'] ?? 0,
        ];
    }

    private function visitorsOneMonth()
    {
        $currentPeriod = Period::create(Carbon::today()->startOfMonth(), Carbon::today());
        $currentResults = $this->performQuery('ga:users', 'ga:yearMonth', $currentPeriod);

        [$start, $end] = $this->getPeriodDiff(Carbon::today()->startOfMonth());
        $previousPeriod = Period::create($start, $end);
        $previousResults = $this->performQuery('ga:users', 'ga:yearMonth', $previousPeriod);

        return [
            'previous' => $previousResults,
            'result' => $currentResults,
        ];
    }

    private function visitorsOneYear()
    {
        $currentPeriod = Period::create(Carbon::today()->startOfYear(), Carbon::today());
        $currentResults = $this->performQuery('ga:users', 'ga:year', $currentPeriod);

        [$start, $end] = $this->getPeriodDiff(Carbon::today()->startOfYear());
        $previousPeriod = Period::create($start, $end);
        $previousResults = $this->performQuery('ga:users', 'ga:year', $previousPeriod);

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
        return 'visitors';
    }
}
