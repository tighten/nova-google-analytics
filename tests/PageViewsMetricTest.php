<?php

namespace Tightenco\NovaGoogleAnalytics\Tests;

use Illuminate\Http\Request;
use Tightenco\NovaGoogleAnalytics\PageViewsMetric;

class PageViewsMetricTest extends TestCase
{
    /** @test */
    public function it_can_return_results_for_one_day()
    {
        $pageViews = new PageViewsMetric;
        $request = new Request();
        $request->merge(['range' => '1']);
        $response = $pageViews->calculate($request);

        $this->assertFalse(empty($response));
    }

    /** @test */
    public function one_day_results_contain_previous()
    {
        $pageViews = new PageViewsMetric;
        $request = new Request();
        $request->merge(['range' => '1']);
        $response = $pageViews->calculate($request);

        $this->assertObjectHasAttribute('previous', $response);
    }

    /** @test */
    public function one_day_results_contain_value()
    {
        $pageViews = new PageViewsMetric;
        $request = new Request();
        $request->merge(['range' => '1']);
        $response = $pageViews->calculate($request);

        $this->assertObjectHasAttribute('value', $response);
    }

    /** @test */
    public function it_can_return_results_mtd()
    {
        $pageViews = new PageViewsMetric;
        $request = new Request();
        $request->merge(['range' => 'MTD']);
        $response = $pageViews->calculate($request);

        $this->assertFalse(empty($response));
    }

    /** @test */
    public function mtd_results_contain_previous()
    {
        $pageViews = new PageViewsMetric;
        $request = new Request();
        $request->merge(['range' => 'MTD']);
        $response = $pageViews->calculate($request);

        $this->assertObjectHasAttribute('previous', $response);
    }

    /** @test */
    public function mtd_results_contain_value()
    {
        $pageViews = new PageViewsMetric;
        $request = new Request();
        $request->merge(['range' => 'MTD']);
        $response = $pageViews->calculate($request);

        $this->assertObjectHasAttribute('value', $response);
    }

    /** @test */
    public function it_can_return_results_ytd()
    {
        $pageViews = new PageViewsMetric;
        $request = new Request();
        $request->merge(['range' => 'YTD']);
        $response = $pageViews->calculate($request);

        $this->assertFalse(empty($response));
    }

    /** @test */
    public function ytd_results_contain_previous()
    {
        $pageViews = new PageViewsMetric;
        $request = new Request();
        $request->merge(['range' => 'YTD']);
        $response = $pageViews->calculate($request);

        $this->assertObjectHasAttribute('previous', $response);
    }

    /** @test */
    public function ytd_results_contain_value()
    {
        $pageViews = new PageViewsMetric;
        $request = new Request();
        $request->merge(['range' => 'YTD']);
        $response = $pageViews->calculate($request);

        $this->assertObjectHasAttribute('value', $response);
    }
}
