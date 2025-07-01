<?php

namespace DevShaded\NvdbSpeedLimits\Http;

use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class NvdbApiClient
{
    /**
     * Fetch speed limit data from NVDB API.
     *
     * @param  float  $lat  Latitude of the center point.
     * @param  float  $lon  Longitude of the center point.
     * @param  float  $radius  Radius in degrees to search around the center point.
     * @return array The speed limit data.
     *
     * @throws Exception If the API request fails.
     */
    public static function fetchSpeedLimitData(
        float $lat,
        float $lon,
        float $radius
    ): array {
        $url = static::buildApiUrl($lat, $lon, $radius);

        try {
            $response = Http::timeout(config('nvdb-speed-limits.api.timeout'))
                ->withHeaders(config('nvdb-speed-limits.api.headers'))
                ->retry(3, 1000) // Retry 3 times with 1-second delay
                ->get($url);

            $response->throw();

            return $response->json();

        } catch (RequestException $e) {
            throw new Exception("NVDB API request failed: {$e->getMessage()}");
        }
    }

    /**
     * Build the API URL with the given parameters.
     *
     * @param  float  $lat  Latitude of the center point.
     * @param  float  $lon  Longitude of the center point.
     * @param  float  $radius  Radius in degrees to search around the center point.
     * @return string The complete API URL.
     */
    protected static function buildApiUrl(float $lat, float $lon, float $radius): string
    {
        $baseUrl = config('nvdb-speed-limits.api.base_url').'/vegobjekter/105';

        return $baseUrl.'?'.http_build_query([
            'kartutsnitt' => implode(',', [
                $lon - $radius, // min_lon
                $lat - $radius, // min_lat
                $lon + $radius, // max_lon
                $lat + $radius, // max_lat
            ]),
            'inkluder' => 'egenskaper,lokasjon',
            'srid' => '4326',
        ]);
    }
}
