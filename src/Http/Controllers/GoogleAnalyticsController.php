<?php

namespace Tightenco\NovaGoogleAnalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Tightenco\NovaGoogleAnalytics\File;
use Illuminate\Routing\Controller;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class GoogleAnalyticsController extends Controller
{
    private $headers = ['name', 'path', 'visits', 'unique_visits', 'avg_page_time', 'entrances', 'bounce_rate', 'exit_rate', 'page_value'];

    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $offset = ($request->input('page', 1) - 1) * $limit;
        $searchTerm = $request->input('s', null);

        $analyticsData = app(Analytics::class)->performQuery(
            $this->periodForDuration($request->input('duration', 'week')),
            'ga:users',
            [
                'metrics' => 'ga:pageviews,ga:uniquePageviews,ga:avgTimeOnPage,ga:entrances,ga:bounceRate,ga:exitRate,ga:pageValue',
                'dimensions' => 'ga:pageTitle,ga:pagePath',
                'sort' => ($request->input('sortDirection', 'desc') == 'desc' ? '-' : '').$request->input('sortBy', 'ga:pageviews'),
                'filters' => $searchTerm ? sprintf('ga:pageTitle=@%s,ga:pagePath=@%s', strval($searchTerm), strval($searchTerm)) : null,
            ]
        );

        try {
            $pages = $this->getPages($this->headers, $analyticsData->rows, $offset, $limit);

            return [
                'pages' => $pages,
                'totalPages' => ceil(count($analyticsData->rows)/$limit),
                'hasMore' => ($offset+$limit) < count($analyticsData->rows)
            ];
        } catch (\Throwable $exception) {
            return [
                'pages' => [],
                'totalPages' => 0,
                'hasMore' => false
            ];
        }
    }

    private function getPages($headers, $rows, $offset, $limit): array
    {
        return array_map(
                function ($row) use ($headers) {
                    return array_combine($headers, $row);
                },
                array_slice($rows, $offset, $limit) ?? []);
    }

    private function periodForDuration(string $duration): Period
    {
        $map = [
            'week' => Period::days(7),
            'month' => Period::months(1),
            'year' => Period::years(1),
        ];
        return Arr::get($map, $duration, Period::days(7));
    }
}
