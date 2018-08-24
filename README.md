# Google Analytics integration with Nova

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tightenco/nova-google-analytics.svg?style=flat-square)](https://packagist.org/packages/tightenco/nova-google-analytics)
[![Total Downloads](https://img.shields.io/packagist/dt/tightenco/nova-google-analytics.svg?style=flat-square)](https://packagist.org/packages/tightenco/nova-google-analytics)

Do stuff.

## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require tightenco/nova-google-analytics
```

Next up, you must register the tool with Nova. This is typically done in the `tools` method of the `NovaServiceProvider`.

```php
// in app/Providers/NovaServiceProvider.php

// ...

public function tools()
{
    return [
        // ...
        new \Tightenco\NovaGoogleAnalytics\Tool(),
    ];
}
```

## Usage

Click on the "nova-google-analytics" menu item in your Nova app to see the tool provided by this package.

### Testing

``` bash
composer test
```

### Security

If you discover any security related issues, please email matt@tighten.co instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
