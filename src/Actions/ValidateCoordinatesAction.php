<?php

namespace DevShaded\NvdbSpeedLimits\Actions;

use DevShaded\NvdbSpeedLimits\Exceptions\InvalidCoordinatesException;

class ValidateCoordinatesAction
{
    /**
     * Validate the latitude and longitude coordinates for Norway.
     *
     * @throws InvalidCoordinatesException
     */
    public static function handle(float $latitude, float $longitude): bool
    {
        $bounds = config('nvdb-speed-limits.bounds');
        [$latMin, $latMax] = $bounds['latitude'];
        [$lonMin, $lonMax] = $bounds['longitude'];

        if ($latitude < $latMin || $latitude > $latMax) {
            throw new InvalidCoordinatesException(
                "Latitude must be between {$latMin} and {$latMax} for Norway"
            );
        }

        if ($longitude < $lonMin || $longitude > $lonMax) {
            throw new InvalidCoordinatesException(
                "Longitude must be between {$lonMin} and {$lonMax} for Norway"
            );
        }

        return true;
    }
}
