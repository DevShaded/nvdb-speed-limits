<?php

namespace DevShaded\NvdbSpeedLimits\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DevShaded\NvdbSpeedLimits\NvdbSpeedLimits
 */
class NvdbSpeedLimits extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \DevShaded\NvdbSpeedLimits\NvdbSpeedLimits::class;
    }
}
