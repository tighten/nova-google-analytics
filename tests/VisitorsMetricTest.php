<?php

namespace Tightenco\NovaGoogleAnalytics\Tests;

use Tightenco\NovaGoogleAnalytics\VisitorsMetric;

class VisitorsMetricTest extends TestCase
{
    /** @test */
    public function it_can_return_results_for_one_day()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => '1']));
        $this->assertFalse(empty($response));
    }

    /** @test */
    public function one_day_results_contain_previous()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => '1']));
        $this->assertObjectHasAttribute('previous', $response);
    }

    /** @test */
    public function one_day_results_contain_value()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => '1']));
        $this->assertObjectHasAttribute('value', $response);
    }

    /** @test */
    public function it_can_return_results_for_yesterday()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => 'Y']));
        $this->assertFalse(empty($response));
    }

    /** @test */
    public function yesterday_results_contain_previous()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => 'Y']));
        $this->assertObjectHasAttribute('previous', $response);
    }

    /** @test */
    public function yesterday_results_contain_value()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => 'Y']));
        $this->assertObjectHasAttribute('value', $response);
    }

    /** @test */
    public function it_can_return_results_last_week()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => 'LW']));
        $this->assertFalse(empty($response));
    }

    /** @test */
    public function last_week_results_contain_previous()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => 'LW']));
        $this->assertObjectHasAttribute('previous', $response);
    }

    /** @test */
    public function last_week_results_contain_value()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => 'LW']));
        $this->assertObjectHasAttribute('value', $response);
    }

    /** @test */
    public function it_can_return_results_last_month()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => 'LM']));
        $this->assertFalse(empty($response));
    }

    /** @test */
    public function last_month_results_contain_previous()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => 'LM']));
        $this->assertObjectHasAttribute('previous', $response);
    }

    /** @test */
    public function last_month_results_contain_value()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => 'LM']));
        $this->assertObjectHasAttribute('value', $response);
    }

    /** @test */
    public function it_can_return_results_last_seven_days()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => '7']));
        $this->assertFalse(empty($response));
    }

    /** @test */
    public function last_seven_days_results_contain_previous()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => '7']));
        $this->assertObjectHasAttribute('previous', $response);
    }

    /** @test */
    public function last_seven_days_results_contain_value()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => '7']));
        $this->assertObjectHasAttribute('value', $response);
    }

    /** @test */
    public function it_can_return_results_last_thirty_days()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => '30']));
        $this->assertFalse(empty($response));
    }

    /** @test */
    public function last_thirty_days_results_contain_previous()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => '30']));
        $this->assertObjectHasAttribute('previous', $response);
    }

    /** @test */
    public function last_thirty_days_results_contain_value()
    {
        $response = (new VisitorsMetric)->calculate($this->requestWithQueryParams(['range' => '30']));
        $this->assertObjectHasAttribute('value', $response);
    }
}
