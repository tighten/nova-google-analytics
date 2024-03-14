<?php

namespace Tightenco\NovaGoogleAnalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\OrderBy;
use Spatie\Analytics\Period;

class MostVisitedPagesController extends Controller
{
    public function __invoke(Request $request): array
    {
        return $this->mostVisitedPages($request);
    }

    private function mostVisitedPages(Request $request): array
    {
        $duration = $request->has('duration')
            ? $request->input('duration')
            : 'week';

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

        $analyticsData = Analytics::get(
            $period,
            ['screenPageViews'],
            ['pageTitle','hostName','pagePath'],
            10,
            [OrderBy::metric('screenPageViews', true)],
        );

        $headers = collect([
            'name',
            'hostname',
            'path',
            'visits',
        ]);

        return $analyticsData
            ->map(fn ($data) => $headers->combine($data))
            ->toArray();
    }
}
