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
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        // Register the main class as a singleton
        $this->app->singleton(NvdbSpeedLimits::class, function ($app) {
            return new NvdbSpeedLimits;
        });

        // Register the facade aliases
        $this->app->alias(\DevShaded\NvdbSpeedLimits\Facades\NvdbSpeedLimits::class, 'NvdbSpeedLimits');
        $this->app->alias(NvdbSpeedLimits::class, 'nvdb-speed-limits');
    }
}
