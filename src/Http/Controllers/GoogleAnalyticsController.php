<?php

namespace Tightenco\NovaGoogleAnalytics\Http\Controllers;

use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy\OrderType;
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
            $analyticsQuery = new AnalyticsQuery([
                'headers' => collect([
                    [
                        'type' => 'dimension',
                        'apiName' => 'pageTitle',
                        'orderType' => OrderType::CASE_INSENSITIVE_ALPHANUMERIC,
                    ],
                    [
                        'type' => 'dimension',
                        'apiName' => 'pagePath',
                        'orderType' => OrderType::ORDER_TYPE_UNSPECIFIED,
                    ],
                    [
                        'type' => 'dimension',
                        'apiName' => 'percentScrolled',
                        'orderType' => OrderType::NUMERIC,
                    ],
                    [
                        'type' => 'metric',
                        'apiName' => 'totalUsers',
                        'orderType' => null,
                    ],
                    [
                        'type' => 'metric',
                        'apiName' => 'newUsers',
                        'orderType' => null,
                    ],
                    [
                        'type' => 'metric',
                        'apiName' => 'screenPageViews',
                        'orderType' => null,
                    ],
                    [
                        'type' => 'metric',
                        'apiName' => 'screenPageViewsPerSession',
                        'orderType' => null,
                    ],
                    [
                        'type' => 'metric',
                        'apiName' => 'userEngagementDuration',
                        'orderType' => null,
                    ],
                    [
                        'type' => 'metric',
                        'apiName' => 'eventCount',
                        'orderType' => null,
                    ],
                    [
                        'type' => 'metric',
                        'apiName' => 'conversions',
                        'orderType' => null,
                    ],
                ]),
                'limit' => $limit = $request->input('limit', 10),
                'offset' => ($request->input('page', 1) - 1) * $limit,
                'searchTerm' => $request->input('s', null),
                'sortDirection' => $request->input('sortDirection', 'desc'),
                'sortBy' => $request->input('sortBy', 'screenPageViews'),
                'duration' => $request->input('duration', 'week'),
                'property' => $request->input('property'),
            ]);

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
