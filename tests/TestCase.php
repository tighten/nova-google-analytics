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

        Storage::fake('testing-storage');

        Storage::disk('testing-storage')
            ->put('test-credentials.json', json_encode($this->get_credentials()));

        $credentials_json_file_path = Storage::disk('testing-storage')
            ->getDriver()
            ->getAdapter()
            ->applyPathPrefix('test-credentials.json');

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

    protected function get_credentials()
    {
        return [
            'type' => 'service_account',
            'project_id' => getenv('ANALYTICS_PROJECT_ID'),
            'private_key_id' => getenv('ANALYTICS_PRIVATE_KEY_ID'),
            'private_key' => getenv('ANALYTICS_PRIVATE_KEY'),
            'client_email' => getenv('ANALYTICS_CLIENT_EMAIL_KEY'),
            'client_id' => getenv('ANALYTICS_CLIENT_ID'),
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://accounts.google.com/o/oauth2/token',
            'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            'client_x509_cert_url' => getenv('ANALYTICS_CLIENT_CERT_URL'),
        ];
    }
}
