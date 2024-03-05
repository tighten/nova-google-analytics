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
        try {
            $analyticsQuery = new AnalyticsQuery(
                $limit = $request->input('limit', 10),
                ($request->input('page', 1) - 1) * $limit,
                $request->input('s', null),
                $request->input('sortDirection', 'desc') == 'desc' ? '-' : '',
                $request->input('sortBy', 'screenPageViews'),
                $request->input('duration', 'week')
            );

            return [
                'pageData' => $analyticsQuery->getPageData(),
                'totalPages' => $analyticsQuery->totalPages(),
                'hasMore' => $analyticsQuery->hasMore(),
            ];
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());

            return [
                'pageData' => [],
                'totalPages' => 0,
                'hasMore' => false,
            ];
        }
    }
}
