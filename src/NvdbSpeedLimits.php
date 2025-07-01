<?php

namespace DevShaded\NvdbSpeedLimits;

use DevShaded\NvdbSpeedLimits\Actions\GetSpeedLimitAction;
use DevShaded\NvdbSpeedLimits\Actions\GetSpeedLimitWithExpandedSearchAction;
use DevShaded\NvdbSpeedLimits\Data\SpeedLimitResult;
use DevShaded\NvdbSpeedLimits\Exceptions\InvalidCoordinatesException;

class NvdbSpeedLimits
{
    /**
     * Get speed limit for a specific latitude and longitude
     *
     * @param  float  $latitude  Latitude in decimal degrees
     * @param  float  $longitude  Longitude in decimal degrees
     * @param  float|null  $radius  Optional search radius in meters
     *
     * @throws InvalidCoordinatesException
     */
    public static function getSpeedLimit(
        float $latitude,
        float $longitude,
        ?float $radius = null
    ): SpeedLimitResult {
        return GetSpeedLimitAction::handle($latitude, $longitude, $radius);
    }

    /**
     * Get speed limit with an expanded search radius
     *
     * This method will attempt to find speed limits starting from a default radius
     * and expanding it until a maximum radius is reached, using a multiplier to increase the radius.
     *
     * @param  float  $latitude  Latitude in decimal degrees
     * @param  float  $longitude  Longitude in decimal degrees
     *
     * @throws InvalidCoordinatesException
     */
    public static function getSpeedLimitWithExpandedSearch(
        float $latitude,
        float $longitude
    ): SpeedLimitResult {
        return GetSpeedLimitWithExpandedSearchAction::handle($latitude, $longitude);
    }

    /**
     * Get speed limits for multiple coordinates
     *
     * @param  array  $coordinates  An array of coordinates, each with 'lat' and 'lng' keys
     * @return array An array of results for each coordinate
     */
    public static function getSpeedLimitsForCoordinates(array $coordinates): array
    {
        $results = [];

        foreach ($coordinates as $coordinate) {
            $lat = $coordinate['lat'] ?? $coordinate['latitude'] ?? null;
            $lng = $coordinate['lng'] ?? $coordinate['longitude'] ?? null;

            if ($lat === null || $lng === null) {
                $results[] = [
                    'coordinate' => $coordinate,
                    'error' => 'Invalid coordinate format',
                    'result' => null,
                ];

                continue;
            }

            try {
                $result = static::getSpeedLimit($lat, $lng);
                $results[] = [
                    'coordinate' => ['lat' => $lat, 'lng' => $lng],
                    'error' => null,
                    'result' => $result,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'coordinate' => ['lat' => $lat, 'lng' => $lng],
                    'error' => $e->getMessage(),
                    'result' => null,
                ];
            }
        }

        return $results;
    }
}
