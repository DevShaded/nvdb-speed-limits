<?php

namespace DevShaded\NvdbSpeedLimits\Actions;

use DevShaded\NvdbSpeedLimits\Data\SpeedLimitResult;
use DevShaded\NvdbSpeedLimits\Exceptions\InvalidCoordinatesException;
use DevShaded\NvdbSpeedLimits\Http\NvdbApiClient;

class GetSpeedLimitAction
{
    /**
     * Fetch speed limit data for given coordinates and radius.
     *
     * @param  float  $latitude  Latitude of the location.
     * @param  float  $longitude  Longitude of the location.
     * @param  float|null  $radius  Search radius in kilometers (optional).
     *
     * @throws InvalidCoordinatesException
     */
    public static function handle(
        float $latitude,
        float $longitude,
        ?float $radius = null
    ): SpeedLimitResult {
        // Validate coordinates
        ValidateCoordinatesAction::handle($latitude, $longitude);

        $radius = $radius ?? config('nvdb-speed-limits.search.default_radius');
        $radiusMeters = (int) round($radius * 111 * 1000);

        try {
            // Fetch data from API
            $data = NvdbApiClient::fetchSpeedLimitData($latitude, $longitude, $radius);

            // Process the response
            return ProcessSpeedLimitDataAction::handle($data, $radiusMeters);

        } catch (\Exception $e) {
            return SpeedLimitResult::notFound(
                $radiusMeters,
                "API request failed: {$e->getMessage()}"
            );
        }
    }
}
