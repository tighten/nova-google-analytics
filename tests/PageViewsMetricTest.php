<?php

namespace Tightenco\NovaGoogleAnalytics\Tests;

use Tightenco\NovaGoogleAnalytics\PageViewsMetric;

class PageViewsMetricTest extends TestCase
{
    /** @test */
    public function it_can_return_results_for_one_day()
    {
        $response = (new PageViewsMetric)->calculate($this->requestWithQueryParams(['range' => '1']));
        $this->assertFalse(empty($response));
    }

    /** @test */
    public function one_day_results_contain_previous()
    {
        $response = (new PageViewsMetric)->calculate($this->requestWithQueryParams(['range' => '1']));
        $this->assertObjectHasProperty('previous', $response);
    }

    /** @test */
    public function one_day_results_contain_value()
    {
        $response = (new PageViewsMetric)->calculate($this->requestWithQueryParams(['range' => '1']));
        $this->assertObjectHasProperty('value', $response);
    }

    /** @test */
    public function it_can_return_results_mtd()
    {
        $response = (new PageViewsMetric)->calculate($this->requestWithQueryParams(['range' => 'MTD']));
        $this->assertFalse(empty($response));
    }

    /** @test */
    public function mtd_results_contain_previous()
    {
        $response = (new PageViewsMetric)->calculate($this->requestWithQueryParams(['range' => 'MTD']));
        $this->assertObjectHasProperty('previous', $response);
    }

    /** @test */
    public function mtd_results_contain_value()
    {
        $response = (new PageViewsMetric)->calculate($this->requestWithQueryParams(['range' => 'MTD']));
        $this->assertObjectHasProperty('value', $response);
    }

    /** @test */
    public function it_can_return_results_ytd()
    {
        $response = (new PageViewsMetric)->calculate($this->requestWithQueryParams(['range' => 'YTD']));
        $this->assertFalse(empty($response));
    }

    /** @test */
    public function ytd_results_contain_previous()
    {
        $response = (new PageViewsMetric)->calculate($this->requestWithQueryParams(['range' => 'YTD']));
        $this->assertObjectHasProperty('previous', $response);
    }

    /** @test */
    public function ytd_results_contain_value()
    {
        $response = (new PageViewsMetric)->calculate($this->requestWithQueryParams(['range' => 'YTD']));
        $this->assertObjectHasProperty('value', $response);
    }
}
