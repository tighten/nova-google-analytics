<?php

namespace Tightenco\NovaGoogleAnalytics\Http\Controllers;

use Tightenco\NovaGoogleAnalytics\File;
use Illuminate\Routing\Controller;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class ReferrerListController extends Controller
{
    public function __invoke()
    {
        return $this->topReferrers();
    }

    private function topReferrers()
    {
        $analyticsData = app(Analytics::class)
            ->performQuery(
                Period::days(7),
                'ga:users',
                [
                    'dimensions' => 'ga:fullReferrer',
                    'sort' => '-ga:users',
                    'max-results' => 10,
                ]
            );

        return collect($analyticsData['rows'] ?? [])->map(function (array $pageRow) {
            return [
                'url' => $pageRow[0],
                'pageViews' => (int) $pageRow[1],
            ];
        });
    }
}
