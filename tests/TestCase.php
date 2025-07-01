<?php

namespace DevShaded\NvdbSpeedLimits\Tests;

use DevShaded\NvdbSpeedLimits\NvdbSpeedLimitsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'DevShaded\\NvdbSpeedLimits\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        // Set up default test configuration
        config([
            'nvdb-speed-limits.api.base_url' => 'https://nvdbapiles-v3.atlas.vegvesen.no',
            'nvdb-speed-limits.api.timeout' => 30,
            'nvdb-speed-limits.api.headers' => [
                'accept' => 'application/vnd.vegvesen.nvdb-v3-rev1+json',
                'X-Client' => 'LaravelNvdbSpeedLimits/1.0',
            ],
            'nvdb-speed-limits.search.default_radius' => 0.0001,
            'nvdb-speed-limits.search.max_radius' => 0.005,
            'nvdb-speed-limits.search.radius_multiplier' => 3,
            'nvdb-speed-limits.bounds' => [
                'latitude' => [57, 72],
                'longitude' => [4, 32],
            ],
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            NvdbSpeedLimitsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
         foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__ . '/database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
         }
         */
    }
}
