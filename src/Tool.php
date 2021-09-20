<?php

namespace Tightenco\NovaGoogleAnalytics;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool as BaseTool;

class Tool extends BaseTool
{

    public function boot()
    {
        Nova::script('nova-google-analytics', __DIR__ . '/../dist/js/tool.js');
        Nova::style('nova-google-analytics', __DIR__ . '/../dist/css/tool.css');
    }

    public function renderNavigation()
    {
        return view('nova-google-analytics::navigation');
    }
}
