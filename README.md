![image](https://repository-images.githubusercontent.com/145988439/fe2db000-7296-11eb-85c9-0c4d22f99125)

# Google Analytics Integration with Nova

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tightenco/nova-google-analytics.svg?style=flat-square)](https://packagist.org/packages/tightenco/nova-google-analytics)
[![Total Downloads](https://img.shields.io/packagist/dt/tightenco/nova-google-analytics.svg?style=flat-square)](https://packagist.org/packages/tightenco/nova-google-analytics)
![Build Status](https://github.com/tighten/nova-google-analytics/actions/workflows/run-tests.yml/badge.svg)

![image](https://user-images.githubusercontent.com/151829/44671717-4a644600-a9f4-11e8-8505-b99e9b9ed65a.png)

<img src="https://user-images.githubusercontent.com/151829/44892455-defbcc00-acb2-11e8-9236-cbc04f1a29eb.png" width="465">

JUST GETTING STARTED.

Plans:

- Analytics tool
- Individual cards for each of the useful analytics data points
- Resource tools (e.g. analytics on each page)
- Maybe actions for events?
- Other great stuff I hope :)

## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require tightenco/nova-google-analytics
```

Next up, you must register the card with Nova. This is typically done in the `cards` method of the `NovaServiceProvider`.

```php
// in app/Providers/NovaServiceProvider.php

// ...

public function cards()
{
    return [
        // ...
        new \Tightenco\NovaGoogleAnalytics\PageViewsMetric,
        new \Tightenco\NovaGoogleAnalytics\VisitorsMetric,
        new \Tightenco\NovaGoogleAnalytics\MostVisitedPagesCard,
        new \Tightenco\NovaGoogleAnalytics\ReferrersList,
    ];
}
```

For now, follow the directions on [Spatie's Laravel Google Analytics package](https://github.com/spatie/laravel-analytics) for getting your credentials, then put them here:

```
yourapp/storage/app/analytics/service-account-credentials.json
```

Also add this to the `.env` for your Nova app:

```ini
ANALYTICS_VIEW_ID=
```

### Security

If you discover any security related issues, please email matt@tighten.co instead of using the issue tracker.

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

Please see [CONTRIBUTING](contributing.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
