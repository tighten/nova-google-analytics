<?php

namespace TightenCo\NovaGoogleAnalytics\Tests;

use TightenCo\NovaGoogleAnalytics\Http\Controllers\ToolController;
use TightenCo\NovaGoogleAnalytics\NovaGoogleAnalytics;
use Symfony\Component\HttpFoundation\Response;

class ToolControllerTest extends TestCase
{
    /** @test */
    public function it_can_can_return_a_response()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/endpoint')
            ->assertSuccessful();
    }
}
