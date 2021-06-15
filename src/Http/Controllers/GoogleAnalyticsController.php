<?php

namespace Tightenco\NovaGoogleAnalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tightenco\NovaGoogleAnalytics\File;
use Illuminate\Routing\Controller;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class GoogleAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->has('limit')
            ? $request->input('limit')
            : 10;
        $page = $request->has('page')
            ? $request->input('page')
            : 1;
        $offset = ($page - 1) * $limit;
        $duration = $request->has('duration')
            ? $request->input('duration')
            : 'week';
        $searchTerm = $request->has('s')
            ? $request->input('s')
            : null;
        $sortBy = $request->has('sortBy')
            ? $request->input('sortBy')
            : 'ga:pageviews';
        $sortDirection = $request->has('sortDirection')
            ? $request->input('sortDirection')
            : 'desc';

        if($searchTerm != null) {
            $filter = 'ga:pageTitle=@'.$searchTerm.',ga:pagePath=@'.$searchTerm;
        }
        else {
            $filter = null;
        }

        switch($duration) {
            case 'week':
                $period = Period::days(7);
                break;
            case 'month':
                $period = Period::months(1);
                break;
            case 'year':
                $period = Period::years(1);
                break;
            default:
                $period = Period::days(7);
                break;
        }

        $analyticsData = app(Analytics::class)->performQuery(
            $period,
            'ga:users',
            [
                'metrics' => 'ga:pageviews,ga:uniquePageviews,ga:avgTimeOnPage,ga:entrances,ga:bounceRate,ga:exitRate,ga:pageValue',
                'dimensions' => 'ga:pageTitle,ga:pagePath',
                'sort' => ($sortDirection == 'desc' ? '-' : '').$sortBy,
                'filters' => $filter,
            ]
        );

        $headers = ['name', 'path', 'visits', 'unique_visits', 'avg_page_time', 'entrances', 'bounce_rate', 'exit_rate', 'page_value'];

        $pages = array_map(
            function ($row) use ($headers) {
                return array_combine($headers, $row);
            },
            array_slice($analyticsData->rows, $offset, $limit) ?? []);

        return [
            'pages' => $pages,
            'totalPages' => ceil(count($analyticsData->rows)/$limit),
            'hasMore' => ($offset+$limit) < count($analyticsData->rows)
        ];
    }
}
