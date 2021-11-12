<?php

namespace Tightenco\NovaGoogleAnalytics;

use Laravel\Nova\Card;

class ReferrersList extends Card
{
    public $width = '1/3';

    public function component(): string
    {
        return 'referrer-list';
    }
}
