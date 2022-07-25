<?php

namespace Tightenco\NovaGoogleAnalytics;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool as BaseTool;

class Tool extends BaseTool
{
    public function boot(): void
    {
        Nova::script('nova-google-analytics', __DIR__ . '/../dist/js/tool.js');
        Nova::style('nova-google-analytics', __DIR__ . '/../dist/css/tool.css');
    }

    public function menu(Request $request): MenuSection
    {
        return MenuSection::make('Google Analytics')
            ->path('/nova-google-analytics')
            ->icon('chart-bar');
    }
}
