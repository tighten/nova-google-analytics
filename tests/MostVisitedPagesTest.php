<?php

namespace Tightenco\NovaGoogleAnalytics\Tests;

use Tightenco\NovaGoogleAnalytics\Http\Controllers\ToolController;
use Tightenco\NovaGoogleAnalytics\NovaGoogleAnalytics;

class MostVisitedPagesTest extends TestCase
{
    /** @test */
    public function it_can_return_a_response()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/most-visited-pages')
            ->assertSuccessful();
    }

    /** @test */
    public function it_returns_ten_results()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/most-visited-pages')
            ->assertJsonCount(10);
    }

    /** @test */
    public function it_returns_correct_json()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/most-visited-pages')
            ->assertJsonStructure([
                '*' => [
                    'hostname',
                    'name',
                    'path',
                    'visits',
                ],
            ]);
    }
}
