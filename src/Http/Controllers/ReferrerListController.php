<?php

namespace Tightenco\NovaGoogleAnalytics\Http\Controllers;

use Illuminate\Http\Request;
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
            ->fetchTopReferrers(Period::days(1));

        return $analyticsData;
    }
}
