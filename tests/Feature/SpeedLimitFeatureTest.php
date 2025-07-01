<?php

declare(strict_types=1);

use DevShaded\NvdbSpeedLimits\Exceptions\InvalidCoordinatesException;
use DevShaded\NvdbSpeedLimits\Facades\NvdbSpeedLimits;
use Illuminate\Support\Facades\Http;

describe('Speed limit feature', function () {
    beforeEach(function () {
        // Set up test configuration
        config([
            'nvdb-speed-limits.api.base_url' => 'https://nvdbapiles-v3.atlas.vegvesen.no',
            'nvdb-speed-limits.api.timeout' => 30,
            'nvdb-speed-limits.api.headers' => [
                'accept' => 'application/vnd.vegvesen.nvdb-v3-rev1+json',
                'X-Client' => 'LaravelNvdbSpeedLimits/1.0',
            ],
            'nvdb-speed-limits.search.default_radius' => 0.0001,
            'nvdb-speed-limits.search.max_radius' => 0.005,
            'nvdb-speed-limits.search.radius_multiplier' => 3,
            'nvdb-speed-limits.bounds' => [
                'latitude' => [57, 72],
                'longitude' => [4, 32],
            ],
        ]);
    });

    it('returns speed limit for valid coordinates', function () {
        $lat = 59.9139;
        $lon = 10.7522;
        $mockResponse = [
            'metadata' => ['antall' => 1],
            'objekter' => [
                [
                    'id' => 1,
                    'egenskaper' => [
                        ['navn' => 'Fartsgrense', 'verdi' => 50],
                    ],
                    'lokasjon' => [
                        'vegsystemreferanser' => [
                            [
                                'vegsystem' => [
                                    'vegkategori' => 'R',
                                    'nummer' => '18',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        Http::fake([
            'nvdbapiles-v3.atlas.vegvesen.no/*' => Http::response($mockResponse, 200),
        ]);

        $result = NvdbSpeedLimits::getSpeedLimit($lat, $lon);

        expect($result->found)->toBeTrue()
            ->and($result->count)->toBe(1)
            ->and($result->speedLimits[0]['speed'])->toBe(50)
            ->and($result->speedLimits[0]['road'])->toBe('Riksvei 18');
    });

    it('returns not found for area with no speed limits', function () {
        $lat = 59.9139;
        $lon = 10.7522;
        $mockResponse = [
            'metadata' => ['antall' => 0],
            'objekter' => [],
        ];

        Http::fake([
            'nvdbapiles-v3.atlas.vegvesen.no/*' => Http::response($mockResponse, 200),
        ]);

        $result = NvdbSpeedLimits::getSpeedLimit($lat, $lon);

        expect($result->found)->toBeFalse()
            ->and($result->count)->toBe(0)
            ->and($result->speedLimits)->toBe([]);
    });

    it('throws exception for invalid coordinates', function () {
        $lat = 56.0; // Out of bounds
        $lon = 10.7522;

        expect(/**
         * @throws InvalidCoordinatesException
         */ fn () => NvdbSpeedLimits::getSpeedLimit($lat, $lon))
            ->toThrow(InvalidCoordinatesException::class);
    });
});
