<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Value;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class PageViewsMetric extends Value
{
    public $name = 'GA Page Views Today';

    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        // $lookups = [
        //     1 => $this->pageViewsOneDay(),
        //     30 => $this->pageViewsOneMonth(),
        // ];

        // $data = array_get($lookups, $request->get('range'), ['result' => 0, 'previous' => 0]);

        $data = $this->pageViewsOneDay();

        return $this
            ->result($data['result'])
            ->previous($data['previous']);
    }

    private function pageViewsOneDay()
    {
        $analyticsData = app(Analytics::class)
            ->fetchTotalVisitorsAndPageViews(Period::days(1));

        return [
            'previous' => $analyticsData->first()['pageViews'],
            'result' => $analyticsData->last()['pageViews'],
        ];
    }

    private function pageViewsOneMonth()
    {
        // $analyticsData = app(Analytics::class)->performQuery(
        //     Period::months(1), // @todo probably two months so we can get both but not sure
        //     'ga:pageviews', // @todo what's the diff between this and the metrics list?
        //     [
        //         'metrics' => 'ga:sessions, ga:pageviews',
        //         'dimensions' => 'ga:yearMonth' // @todo is the the right dimension?
        //     ]
        // );
        // @todo figure out how to process from class Google_Service_Analytics_GaData
        //

        return ['result' => 999, 'previous' => 999];
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            // 1 => '1 day',
            // 30 => '1 month',
            // 60 => '60 Days',
            // 365 => '365 Days',
            // 'MTD' => 'Month To Date',
            // 'QTD' => 'Quarter To Date',
            // 'YTD' => 'Year To Date',
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(30);
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
