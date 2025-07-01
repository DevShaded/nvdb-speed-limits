# Fetch Norwegian speed limits from NVDB API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/devshaded/nvdb-speed-limits.svg?style=flat-square)](https://packagist.org/packages/devshaded/nvdb-speed-limits)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/devshaded/nvdb-speed-limits/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/devshaded/nvdb-speed-limits/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/devshaded/nvdb-speed-limits/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/devshaded/nvdb-speed-limits/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/devshaded/nvdb-speed-limits.svg?style=flat-square)](https://packagist.org/packages/devshaded/nvdb-speed-limits)

A Laravel package for fetching speed limits from the Norwegian NVDB API.
## Installation

You can install the package via composer:

```bash
composer require devshaded/nvdb-speed-limits
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="nvdb-speed-limits-config"
```

This is the contents of the published config file:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | NVDB API Configuration
    |--------------------------------------------------------------------------
    */
    'api' => [
        'base_url' => 'https://nvdbapiles-v3.atlas.vegvesen.no',
        'timeout' => 30,
        'headers' => [
            'accept' => 'application/vnd.vegvesen.nvdb-v3-rev1+json',
            'X-Client' => 'LaravelNvdbSpeedLimits/1.0',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Configuration
    |--------------------------------------------------------------------------
    */
    'search' => [
        'default_radius' => 0.0001, // ~11 meters
        'max_radius' => 0.005,         // ~550 meters
        'radius_multiplier' => 3, // For expanding search
    ],

    /*
    |--------------------------------------------------------------------------
    | Coordinate Validation for Norway
    |--------------------------------------------------------------------------
    */
    'bounds' => [
        'latitude' => [
            57,
            72,
        ],
        'longitude' => [
            4,
            32,
        ],
    ],
];
```

## Usage

```php
use DevShaded\NvdbSpeedLimits\Facades\NvdbSpeedLimits;

$result = NvdbSpeedLimits::getSpeedLimit(59.9139, 10.7522);

if ($result->found) {
    return "Speed limit: " . $result->recommended['speed'] . " km/h";
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [DevShaded](https://github.com/DevShaded)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
