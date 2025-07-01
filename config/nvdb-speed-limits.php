<?php

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
