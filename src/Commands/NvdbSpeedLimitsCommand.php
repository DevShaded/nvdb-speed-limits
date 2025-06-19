<?php

namespace DevShaded\NvdbSpeedLimits\Commands;

use Illuminate\Console\Command;

class NvdbSpeedLimitsCommand extends Command
{
    public $signature = 'nvdb-speed-limits';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
