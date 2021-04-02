<?php

namespace Tightenco\NovaGoogleAnalytics\Tests;

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as Orchestra;
use Tightenco\NovaGoogleAnalytics\CardServiceProvider;
use Tightenco\NovaGoogleAnalytics\ToolServiceProvider;
use Spatie\Analytics\AnalyticsFacade;
use Spatie\Analytics\AnalyticsServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
        Route::middlewareGroup('nova', []);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->useEnvironmentPath(__DIR__ . '/../../..');
        $app->useStoragePath(realpath(__DIR__ . '/../../../storage'));
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        $app->config->set('analytics.view_id', getenv('ANALYTICS_VIEW_ID'));
        $app->config->set('analytics.service_account_credentials_json', storage_path() . '/app/analytics/service-account-credentials.json');

        parent::getEnvironmentSetUp($app);
    }

    protected function getPackageProviders($app)
    {
        return [
            ToolServiceProvider::class,
            CardServiceProvider::class,
            AnalyticsServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Analytics' => AnalyticsFacade::class,
        ];
    }

    protected function requestWithQueryParams(array $params): Request
    {
        $request = new Request();
        $request->merge($params);
        return $request;
    }
}
