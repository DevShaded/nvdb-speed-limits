<?php

namespace DevShaded\NvdbSpeedLimits\Actions;

use DevShaded\NvdbSpeedLimits\Data\SpeedLimitResult;

class ProcessSpeedLimitDataAction
{
    /**
     * Process the raw data from the NVDB API and extract speed limits.
     *
     * @param  array  $data  The raw data from the NVDB API.
     * @param  int  $radiusMeters  The radius in meters for which speed limits are requested.
     */
    public static function handle(array $data, int $radiusMeters): SpeedLimitResult
    {
        $count = $data['metadata']['antall'] ?? 0;

        if ($count === 0 || empty($data['objekter'])) {
            return SpeedLimitResult::notFound($radiusMeters);
        }

        $speedLimits = self::extractSpeedLimits($data['objekter']);

        if (empty($speedLimits)) {
            return SpeedLimitResult::notFound(
                $radiusMeters,
                'Objects found but no speed limits extracted'
            );
        }

        $recommended = self::selectRecommendedSpeedLimit($speedLimits);

        return SpeedLimitResult::found(
            count: count($speedLimits),
            radiusMeters: $radiusMeters,
            speedLimits: $speedLimits,
            recommended: $recommended
        );
    }

    /**
     * Extract speed limits from the NVDB API objects.
     *
     * @param  array  $objects  The objects from the NVDB API response.
     */
    protected static function extractSpeedLimits(array $objects): array
    {
        $speedLimits = [];

        foreach ($objects as $obj) {
            $speedProp = collect($obj['egenskaper'] ?? [])
                ->firstWhere('navn', 'Fartsgrense');

            if (! $speedProp) {
                continue;
            }

            $roadInfo = static::extractRoadInfo($obj);

            $speedLimits[] = [
                'id' => $obj['id'],
                'speed' => (int) $speedProp['verdi'],
                'road' => $roadInfo['name'],
                'road_type' => $roadInfo['type'],
                'road_number' => $roadInfo['number'],
                'category' => $roadInfo['category'],
            ];
        }

        return $speedLimits;
    }

    /**
     * Extract road information from the NVDB API object.
     *
     * @param  array  $obj  The NVDB API object containing road information.
     */
    protected static function extractRoadInfo(array $obj): array
    {
        $default = [
            'name' => 'Unknown road',
            'type' => 'Unknown',
            'number' => '',
            'category' => null,
        ];

        $references = $obj['lokasjon']['vegsystemreferanser'] ?? [];

        foreach ($references as $ref) {
            $category = $ref['vegsystem']['vegkategori'] ?? null;
            $number = $ref['vegsystem']['nummer'] ?? '';

            if ($category) {
                $type = static::getRoadTypeName($category);

                return [
                    'name' => trim("{$type} {$number}"),
                    'type' => $type,
                    'number' => $number,
                    'category' => $category,
                ];
            }
        }

        return $default;
    }

    /**
     * Get the human-readable name for the road type based on its category.
     *
     * @param  string  $category  The road category code.
     */
    protected static function getRoadTypeName(string $category): string
    {
        return match ($category) {
            'R' => 'Riksvei',
            'F' => 'Fylkesvei',
            'K', 'S' => 'Kommunal vei',
            'P' => 'Privat vei',
            default => 'Unknown road type',
        };
    }

    /**
     * Select the recommended speed limit from the available speed limits.
     *
     * @param  array  $speedLimits  The list of speed limits.
     * @return array|null The recommended speed limit or null if none found.
     */
    protected static function selectRecommendedSpeedLimit(array $speedLimits): ?array
    {
        if (count($speedLimits) === 1) {
            return $speedLimits[0];
        }

        // Prioritize main roads (R, F) over local roads
        $mainRoads = array_filter(
            $speedLimits,
            fn ($limit) => in_array($limit['category'], ['R', 'F'])
        );

        if (count($mainRoads) === 1) {
            return $mainRoads[0];
        }

        return $speedLimits[0] ?? null;
    }
}
