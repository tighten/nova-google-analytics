<?php

namespace Tightenco\NovaGoogleAnalytics;

class GoogleAnalyticsClient
{
    public string $property;

    public function __construct($property)
    {
        $this->property = $property;
    }

}
