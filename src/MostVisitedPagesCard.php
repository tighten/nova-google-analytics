<?php

namespace Tightenco\NovaGoogleAnalytics;

use Laravel\Nova\Card;

class MostVisitedPagesCard extends Card
{
    public $width = '1/3';

    public function component(): string
    {
        return 'most-visited-pages';
    }
}
