<?php

namespace Tightenco\NovaGoogleAnalytics\Tests;

class TopReferrersTest extends TestCase
{
    /** @test */
    public function it_can_return_a_response()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/referrer-list')
            ->assertSuccessful();
    }

    /** @test */
    public function it_returns_ten_results()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/referrer-list')
            ->assertJsonCount(10);
    }

    /** @test */
    public function it_returns_correct_json()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/referrer-list')
            ->assertJsonStructure([
                '*' => [
                    'pageViews',
                    'url',
                ],
            ]);
    }
}
