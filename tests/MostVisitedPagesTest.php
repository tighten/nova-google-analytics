<?php

namespace Tightenco\NovaGoogleAnalytics\Tests;

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
    public function it_returns_ten_results_max()
    {
        $results = $this->get('nova-vendor/tightenco/nova-google-analytics/most-visited-pages')->getData();

        $this->assertLessThanOrEqual(10, count($results));
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
