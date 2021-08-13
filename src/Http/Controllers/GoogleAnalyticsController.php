<?php

namespace Tightenco\NovaGoogleAnalytics\Http\Controllers;

use Illuminate\Http\Request;
use Tightenco\NovaGoogleAnalytics\AnalyticsQuery;
use Tightenco\NovaGoogleAnalytics\File;
use Illuminate\Routing\Controller;
use Throwable;

class GoogleAnalyticsController extends Controller
{
    public function index(Request $request)
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
                'hasMore' => $analyticsQuery->hasMore()
            ];
        } catch (Throwable $exception) {
            return [
                'pages' => [],
                'totalPages' => 0,
                'hasMore' => false
            ];
        }
    }
}
