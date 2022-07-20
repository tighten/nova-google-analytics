<?php

namespace Tightenco\NovaGoogleAnalytics\Tests;

use Spatie\Analytics\Period;
use Tightenco\NovaGoogleAnalytics\AnalyticsQuery;

class ToolTest extends TestCase
{
    protected array $failedJsonResponse = [
        'pageData' => [],
        'totalPages' => 0,
        'hasMore' => false,
    ];

    /** @test */
    public function it_can_return_a_response()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/pages')
            ->assertSuccessful()
            ->assertJsonStructure([
                'hasMore',
                'pageData',
                'totalPages',
            ]);
    }

    /** @test */
    public function it_will_accept_limit_param()
    {
        $results = $this->get('nova-vendor/tightenco/nova-google-analytics/pages?limit=5')->getData();

        $this->assertLessThanOrEqual(5, count($results->pageData));
    }

    /** @test */
    public function it_will_not_accept_non_numeric_limit()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/pages?limit=a')
            ->assertJson($this->failedJsonResponse);
    }

    /** @test */
    public function it_will_accept_page_param()
    {
        $page1 = $this->get('nova-vendor/tightenco/nova-google-analytics/pages?page=1');
        $page2 = $this->get('nova-vendor/tightenco/nova-google-analytics/pages?page=2');

        $this->assertNotEquals($page1, $page2);
    }

    /** @test */
    public function it_will_not_accept_non_numeric_page()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/pages?page=a')
            ->assertJson($this->failedJsonResponse);
    }

    /** @test */
    public function it_will_accept_duration_param()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/pages?duration=month')
            ->assertSuccessful();
    }

    /** @test */
    public function invalid_duration_will_default_to_week()
    {
        $analyticsQuery = new AnalyticsQuery(
            [],
            1,
            0,
            '',
            '-',
            'ga:pageviews',
            'asdf'
        );

        $this->assertEquals(Period::days(7), $analyticsQuery->getDuration());
    }

    /** @test */
    public function it_will_accept_search_param()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/pages?s=blog')
            ->assertSuccessful();
    }

    /** @test */
    public function it_will_accept_sort_param()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/pages?sortBy=ga:pageValue')
            ->assertSuccessful();
    }

    /** @test */
    public function it_will_not_accept_invalid_sort_param()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/pages?sortBy=ga:123')
            ->assertJson($this->failedJsonResponse);
    }

    /** @test */
    public function it_will_accept_sort_direction_param()
    {
        $this
            ->get('nova-vendor/tightenco/nova-google-analytics/pages?sortDirection=asc')
            ->assertSuccessful();
    }
}
