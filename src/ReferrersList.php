<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Analytics\Analytics;
use Laravel\Nova\Card;
use Spatie\Analytics\Period;
use Laravel\Nova\Metrics\Value;

class ReferrersList extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = '1/3';

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'referrer-list';
    }
}
