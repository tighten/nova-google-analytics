<?php

namespace Tightenco\NovaGoogleAnalytics\Http\Controllers;

use Illuminate\Http\Request;
use Tightenco\NovaGoogleAnalytics\File;
use Illuminate\Routing\Controller;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class MostVisitedPagesController extends Controller
{
    public function __invoke()
    {
        return $this->mostVisitedPages();
    }

    private function mostVisitedPages()
    {
        $analyticsData = app(Analytics::class)->performQuery(
            Period::days(7),
            'ga:users',
            [
                'metrics' => 'ga:pageviews',
                'dimensions' => 'ga:pageTitle,ga:hostname,ga:pagePath',
                'sort' => '-ga:pageviews',
                'max-results' => 10,
            ]
        );

        $headers = ['name', 'hostname', 'path', 'visits'];

        return array_map(
            function ($row) use ($headers) {
                return array_combine($headers, $row);
            },
            $analyticsData->rows
        );
    }
}
