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
    use MetricDiffTrait;

    public function name()
    {
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
            1 => $this->pageViewsToday(),
            'Y' => $this->pageViewsYesterday(),
            'LW' =>$this->pageViewsLastWeek(),
            'LM' => $this->pageViewsLastMonth(),
            7 => $this->pageViewsLastSevenDays(),
            30 => $this->pageViewsLastThirtyDays(),
        ];

        $data = Arr::get($lookups, $request->get('range'), ['result' => 0, 'previous' => 0]);

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
        $currentPeriod = Period::create(
            Carbon::today()
                ->clone()
                ->startOfWeek(Carbon::SUNDAY)
                ->subWeek(),
            Carbon::today()
                ->clone()
                ->subWeek()
                ->endOfWeek(Carbon::SATURDAY)
        );
        $currentResults = $this->performQuery('ga:pageviews', 'ga:date', $currentPeriod);

        $previousPeriod = Period::create(
            Carbon::today()
                ->clone()
                ->startOfWeek(Carbon::SUNDAY)
                ->subWeeks(2),
            Carbon::today()
                ->clone()
                ->subWeeks(2)
                ->endOfWeek(Carbon::SATURDAY)
        );
        $previousResults = $this->performQuery('ga:pageviews', 'ga:date', $previousPeriod);

        return [
            'previous' => $previousResults->pluck('value')->sum(),
            'result' => $currentResults->pluck('value')->sum(),
        ];
    }

    private function pageViewsLastMonth(): array
    {
        $currentResults = $this->performQuery('ga:pageviews', 'ga:date', $this->getLastMonth()['current']);
        $previousResults = $this->performQuery('ga:pageviews', 'ga:date', $this->getLastMonth()['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum(),
            'result' => $currentResults->pluck('value')->sum(),
        ];
    }

    private function pageViewsLastSevenDays(): array
    {
        $currentResults = $this->performQuery('ga:pageviews', 'ga:date', $this->getLastSevenDays()['current']);
        $previousResults = $this->performQuery('ga:pageviews', 'ga:date', $this->getLastSevenDays()['previous']);

        return [
            'previous' => $previousResults->pluck('value')->sum(),
            'result' => $currentResults->pluck('value')->sum(),
        ];
    }

    private function pageViewsLastThirtyDays(): array
    {
        $currentResults = $this->performQuery('ga:pageviews', 'ga:date', $this->getLastThirtyDays()['current']);
        $previousResults = $this->performQuery('ga:pageviews', 'ga:date', $this->getLastThirtyDays()['previous']);

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
        return 'page-views';
    }
}
