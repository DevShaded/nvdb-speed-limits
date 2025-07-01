<?php

declare(strict_types=1);

use DevShaded\NvdbSpeedLimits\Data\SpeedLimitResult;
use DevShaded\NvdbSpeedLimits\Exceptions\InvalidCoordinatesException;
use DevShaded\NvdbSpeedLimits\NvdbSpeedLimits;
use Illuminate\Support\Facades\Http;

describe('NvdbSpeedLimits', function () {
    beforeEach(function () {
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

    it('getSpeedLimit returns a SpeedLimitResult for valid coordinates', function () {
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
        expect($result)->toBeInstanceOf(SpeedLimitResult::class)
            ->and($result->found)->toBeTrue();
    });

    it('getSpeedLimitWithExpandedSearch returns a SpeedLimitResult', function () {
        $lat = 59.9139;
        $lon = 10.7522;
        $mockResponse = [
            'metadata' => ['antall' => 1],
            'objekter' => [
                [
                    'id' => 1,
                    'egenskaper' => [
                        ['navn' => 'Fartsgrense', 'verdi' => 60],
                    ],
                    'lokasjon' => [
                        'vegsystemreferanser' => [
                            [
                                'vegsystem' => [
                                    'vegkategori' => 'F',
                                    'nummer' => '150',
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

        $result = NvdbSpeedLimits::getSpeedLimitWithExpandedSearch($lat, $lon);
        expect($result)->toBeInstanceOf(SpeedLimitResult::class)
            ->and($result->found)->toBeTrue();
    });

    it('getSpeedLimitsForCoordinates returns results for multiple coordinates', function () {
        $coordinates = [
            ['lat' => 59.9139, 'lng' => 10.7522],
            ['lat' => 60.0, 'lng' => 11.0],
            ['lat' => 56.0, 'lng' => 10.0], // Invalid
        ];

        Http::fake([
            'nvdbapiles-v3.atlas.vegvesen.no/*' => Http::response([
                'metadata' => ['antall' => 1],
                'objekter' => [
                    [
                        'id' => 1,
                        'egenskaper' => [
                            ['navn' => 'Fartsgrense', 'verdi' => 70],
                        ],
                        'lokasjon' => [
                            'vegsystemreferanser' => [
                                [
                                    'vegsystem' => [
                                        'vegkategori' => 'R',
                                        'nummer' => '4',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $results = NvdbSpeedLimits::getSpeedLimitsForCoordinates($coordinates);
        expect($results)->toHaveCount(3)
            ->and($results[0]['result'])->toBeInstanceOf(SpeedLimitResult::class)
            ->and($results[0]['error'])->toBeNull()
            ->and($results[2]['error'])->toBe('Latitude must be between 57 and 72 for Norway');
    });

    it('getSpeedLimit throws for invalid coordinates', function () {
        $lat = 56.0;
        $lon = 10.0;
        expect(fn () => NvdbSpeedLimits::getSpeedLimit($lat, $lon))
            ->toThrow(InvalidCoordinatesException::class);
    });
});
