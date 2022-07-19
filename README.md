![image](https://repository-images.githubusercontent.com/145988439/fe2db000-7296-11eb-85c9-0c4d22f99125)

# Google Analytics Integration with Nova

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tightenco/nova-google-analytics.svg?style=flat-square)](https://packagist.org/packages/tightenco/nova-google-analytics)
[![Total Downloads](https://img.shields.io/packagist/dt/tightenco/nova-google-analytics.svg?style=flat-square)](https://packagist.org/packages/tightenco/nova-google-analytics)
![Build Status](https://github.com/tighten/nova-google-analytics/actions/workflows/run-tests.yml/badge.svg)

## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require tightenco/nova-google-analytics
```

For now, follow the directions on [Spatie's Laravel Google Analytics package](https://github.com/spatie/laravel-analytics) for getting your credentials, then put them here:

```
yourapp/storage/app/analytics/service-account-credentials.json
```

Also add this to the `.env` for your Nova app:

```ini
ANALYTICS_VIEW_ID=
```

## Usage
You must register the cards you want to display with Nova. This is typically done in the `cards` method of the `Main`
dashboard.

```php
// in app/Nova/Dashboards/Main.php

// ...

public function cards()
{
    return [
        // ...
        new \Tightenco\NovaGoogleAnalytics\PageViewsMetric,
        new \Tightenco\NovaGoogleAnalytics\VisitorsMetric,
        new \Tightenco\NovaGoogleAnalytics\MostVisitedPagesCard,
        new \Tightenco\NovaGoogleAnalytics\ReferrersList,
        new \Tightenco\NovaGoogleAnalytics\OneDayActiveUsersMetric,
        new \Tightenco\NovaGoogleAnalytics\SevenDayActiveUsersMetric,
        new \Tightenco\NovaGoogleAnalytics\FourteenDayActiveUsersMetric,
        new \Tightenco\NovaGoogleAnalytics\TwentyEightDayActiveUsersMetric,
        new \Tightenco\NovaGoogleAnalytics\SessionsMetric,
        new \Tightenco\NovaGoogleAnalytics\SessionDurationMetric,
        new \Tightenco\NovaGoogleAnalytics\SessionsByDeviceMetric,
        new \Tightenco\NovaGoogleAnalytics\SessionsByCountryMetric,
    ];
}
```

## Features

#### View the Visitors and Pageview Metrics

![image](https://user-images.githubusercontent.com/7070136/179579307-e61c4fe4-0e70-482d-8939-3a47bc90b604.png)

#### View the Active Users Metrics

![image](https://user-images.githubusercontent.com/7070136/179579376-06054344-ae64-4452-913b-2196f744f41c.png)

#### View the Sessions and Avg. Session Duration Metrics

![image](https://user-images.githubusercontent.com/7070136/179579480-e2e9cbc6-beea-47d8-9268-a68eac90a436.png)

#### View the Devices and Country Metrics by Session

![image](https://user-images.githubusercontent.com/7070136/179579608-61cde3e7-4159-4025-a925-3a2940e94ed6.png)

#### View the lists of Most Visited Pages and Referrers

![image](https://user-images.githubusercontent.com/7070136/179579542-9e60e6a4-53d6-4d40-a9f9-d9aeb3cec791.png)

## Testing

First, copy your .env.example file to a new file called .env.testing:

```
cp .env.example .env.testing
```

Make sure, in that file, to define the following variables to run all tests:

```
ANALYTICS_VIEW_ID
ANALYTICS_PROJECT_ID
ANALYTICS_PRIVATE_KEY_ID
ANALYTICS_PRIVATE_KEY
ANALYTICS_CLIENT_EMAIL_KEY
ANALYTICS_CLIENT_ID
ANALYTICS_CLIENT_CERT_URL
```

Add the `.env.testing` file in `.gitignore` so you can safely use the same analytics view ID to run the tests that you use for the card.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email matt@tighten.co instead of using the issue tracker.

## Credits

- [Matt Stauffer](https://github.com/mattstauffer)
- [Kristin Collins](https://github.com/krievley)
- [All Contributors](https://github.com/tighten/nova-google-analytics/graphs/contributors)

## Support us

Tighten is a web development firm that works in Laravel, Vue, and React. You can learn more about us on our [web site](https://tighten.co/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
