<?php

namespace Tightenco\NovaGoogleAnalytics;

use Illuminate\Contracts\View\View;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool as BaseTool;

class Tool extends BaseTool
{
    public function boot(): void
    {
        Nova::script('nova-google-analytics', __DIR__ . '/../dist/js/tool.js');
        Nova::style('nova-google-analytics', __DIR__ . '/../dist/css/tool.css');
    }

    public function renderNavigation(): View
    {
        return view('nova-google-analytics::navigation');
    }
}
