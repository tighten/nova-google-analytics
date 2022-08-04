<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class AnalyticsProject extends Resource
{
    public static $model = \App\Models\AnalyticsProject::class;

    public static $title = 'id';

    public static $clickAction = 'edit';

    public static $search = [
        'id',
        'project_id',
        'project_name',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make(__('Project ID'), 'project_id')->sortable(),
            Text::make(__('Name'), 'project_name')->sortable(),
            File::make(__('Credentials'), 'credentials')
                ->disk('local')
                ->path('analytics/' . $request->project_id)
                ->storeAs(function () {
                    return 'service-account-credentials.json';
                })
                ->disableDownload()
                ->acceptedTypes('.json'),
            Text::make(__('Gate'), 'gate')->nullable()->sortable(),
        ];
    }

    public function cards(Request $request)
    {
        return [];
    }

    public function filters(Request $request)
    {
        return [];
    }

    public function lenses(Request $request)
    {
        return [];
    }

    public function actions(Request $request)
    {
        return [];
    }
}
