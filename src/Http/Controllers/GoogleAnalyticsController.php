<?php

namespace Tightenco\NovaGoogleAnalytics\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;
use Throwable;
use Tightenco\NovaGoogleAnalytics\AnalyticsQuery;

class GoogleAnalyticsController extends Controller
{
    public function index(Request $request): array
    {
        try {
            $analyticsQuery = new AnalyticsQuery(
                ['name', 'path', 'visits', 'unique_visits', 'avg_page_time', 'entrances', 'bounce_rate', 'exit_rate', 'page_value'],
                $limit = $request->input('limit', 10),
                ($request->input('page', 1) - 1) * $limit,
                $request->input('s', null),
                $request->input('sortDirection', 'desc') == 'desc' ? '-' : '',
                $request->input('sortBy', 'ga:pageviews'),
                $request->input('duration', 'week')
            );

            return [
                'pages' => $analyticsQuery->getPages(),
                'totalPages' => $analyticsQuery->totalPages(),
                'hasMore' => $analyticsQuery->hasMore(),
            ];
        } catch (Throwable $exception) {
            return [
                'pages' => [],
                'totalPages' => 0,
                'hasMore' => false,
            ];
        }
    }

    public function show(Request $request)
    {
        $request->validate([
            'url' => 'required|string',
        ]);

        $period = Period::create(Carbon::today()->startOfMonth(), Carbon::today());

        $analyticsData = app(Analytics::class)->performQuery(
            $period,
            'ga:users',
            [
                'metrics' => 'ga:pageviews',
                'dimensions' => 'ga:pageTitle',
                'filters' => sprintf('ga:pagePath=@%s', strval($request->url)),
                'max-results' => 1,
            ]
        );

        $data = array_combine(['title', 'pageviews'], $analyticsData->rows[0]);
        dd($data);
    }
}