<?php

namespace DevShaded\NvdbSpeedLimits;

use DevShaded\NvdbSpeedLimits\Commands\NvdbSpeedLimitsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NvdbSpeedLimitsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('nvdb-speed-limits')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_nvdb_speed_limits_table')
            ->hasCommand(NvdbSpeedLimitsCommand::class);
    }
}
