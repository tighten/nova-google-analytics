<?php

namespace Tightenco\NovaGoogleAnalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Throwable;
use Tightenco\NovaGoogleAnalytics\AnalyticsQuery;

class GoogleAnalyticsController extends Controller
{
    public function index(Request $request): array
    {
//        try {
        $analyticsQuery = new AnalyticsQuery([
            'dimensions' => ['pageTitle', 'pagePath', 'percentScrolled'],
            'metrics' => ['screenPageViews', 'totalUsers', 'newUsers', 'screenPageViewsPerSession', 'userEngagementDuration', 'eventCount', 'conversions'],
            'limit' => $limit = $request->input('limit', 10),
            'offset' => ($request->input('page', 1) - 1) * $limit,
            'searchTerm' => $request->input('s', null),
            'sortDirection' => $request->input('sortDirection', 'desc') == 'desc' ? '-' : '',
            'sortBy' => $request->input('sortBy', 'ga:pageviews'),
            'duration' => $request->input('duration', 'week'),
            'property' => $request->input('property'),
        ]);

        return [
            'pageData' => $analyticsQuery->getPageData(),
            'totalPages' => $analyticsQuery->totalPages(),
            'hasMore' => $analyticsQuery->hasMore(),
        ];
//        } catch (Throwable $exception) {
//            Log::error($exception->getMessage());
//
//            return [
//                'pageData' => [],
//                'totalPages' => 0,
//                'hasMore' => false,
//            ];
//        }
    }
}
