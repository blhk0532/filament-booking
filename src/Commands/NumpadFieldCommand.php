<?php

namespace Adultdate\FilamentBooking\Commands;

use Illuminate\Console\Command;

class NumpadFieldCommand extends Command
{
    public $signature = 'filament-booking';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
