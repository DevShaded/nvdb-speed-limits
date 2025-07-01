<?php

namespace DevShaded\NvdbSpeedLimits\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SpeedLimitResource
 *
 * @property bool $found
 * @property int $count
 * @property float $radiusMeters
 * @property array $speedLimits
 * @property array $recommended
 * @property string|null $message
 */
class SpeedLimitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'found' => $this->found,
            'count' => $this->count,
            'radius_meters' => $this->radiusMeters,
            'speed_limits' => $this->speedLimits,
            'recommended' => $this->recommended,
            'message' => $this->message,
        ];
    }
}
