<?php

namespace DevShaded\NvdbSpeedLimits\Actions;

use DevShaded\NvdbSpeedLimits\Data\SpeedLimitResult;
use DevShaded\NvdbSpeedLimits\Exceptions\InvalidCoordinatesException;

class GetSpeedLimitWithExpandedSearchAction
{
    /**
     * Handle the action to get speed limits with an expanded search radius.
     *
     * This method will attempt to find speed limits starting from a default radius
     * and expanding it until a maximum radius is reached, using a multiplier to increase the radius.
     *
     * @throws InvalidCoordinatesException
     */
    public static function handle(float $latitude, float $longitude): SpeedLimitResult
    {
        $radius = config('nvdb-speed-limits.search.default_radius');
        $maxRadius = config('nvdb-speed-limits.search.max_radius');
        $multiplier = config('nvdb-speed-limits.search.radius_multiplier');

        while ($radius <= $maxRadius) {
            $result = GetSpeedLimitAction::handle($latitude, $longitude, $radius);

            if ($result->found) {
                return $result;
            }

            $radius *= $multiplier;
        }

        return SpeedLimitResult::notFound(
            (int) round($maxRadius * 111 * 1000),
            'No speed limits found even with expanded search radius'
        );
    }
}
