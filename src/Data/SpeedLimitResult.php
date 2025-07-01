<?php

namespace DevShaded\NvdbSpeedLimits\Data;

class SpeedLimitResult
{
    /**
     * SpeedLimitResult constructor.
     *
     * @param  bool  $found  Indicates if speed limits were found.
     * @param  int  $count  The number of speed limits found.
     * @param  int  $radiusMeters  The radius in meters within which speed limits were searched.
     * @param  array  $speedLimits  An array of speed limit data.
     * @param  array|null  $recommended  Optional recommended speed limit data.
     * @param  string|null  $message  Optional message to include in the result.
     */
    public function __construct(
        public bool $found,
        public int $count,
        public int $radiusMeters,
        public array $speedLimits,
        public ?array $recommended = null,
        public ?string $message = null
    ) {}

    /**
     * When no speed limits are found, create a SpeedLimitResult instance.
     *
     * @param  int  $radiusMeters  The radius in meters within which speed limits were searched.
     * @param  string|null  $message  Optional message to include in the result.
     */
    public static function notFound(int $radiusMeters, ?string $message = null): self
    {
        return new self(
            found: false,
            count: 0,
            radiusMeters: $radiusMeters,
            speedLimits: [],
            recommended: null,
            message: $message ?? 'No speed limits found at this location'
        );
    }

    /**
     * When speed limits are found, create a SpeedLimitResult instance.
     *
     * @param  int  $count  The number of speed limits found.
     * @param  int  $radiusMeters  The radius in meters within which speed limits were searched.
     * @param  array  $speedLimits  An array of speed limit data.
     * @param  array|null  $recommended  Optional recommended speed limit data.
     */
    public static function found(
        int $count,
        int $radiusMeters,
        array $speedLimits,
        ?array $recommended = null
    ): self {
        return new self(
            found: true,
            count: $count,
            radiusMeters: $radiusMeters,
            speedLimits: $speedLimits,
            recommended: $recommended
        );
    }

    /**
     * Get the speed limits found.
     */
    public function getRecommendedSpeed(): ?int
    {
        return $this->recommended['speed'] ?? null;
    }

    /**
     * Get the road name for the recommended speed limit.
     */
    public function getRecommendedRoad(): ?string
    {
        return $this->recommended['road'] ?? null;
    }

    /**
     * Check if there are multiple speed limit options available.
     */
    public function hasMultipleOptions(): bool
    {
        return count($this->speedLimits) > 1;
    }
}
