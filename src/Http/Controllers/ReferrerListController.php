<?php

namespace Tightenco\NovaGoogleAnalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\OrderBy;
use Spatie\Analytics\Period;

class ReferrerListController extends Controller
{
    public function __invoke(Request $request): Collection
    {
        return $this->topReferrers($request);
    }

    private function topReferrers(Request $request): Collection
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
            ['totalUsers'],
            ['pageReferrer'],
            10,
            [OrderBy::dimension('totalUsers',true)],
        );

        return $analyticsData->map(fn ($data) => [
            'url' => $data['pageReferrer'],
            'pageViews' => (int) $data['totalUsers'],
        ]);
    }
}
