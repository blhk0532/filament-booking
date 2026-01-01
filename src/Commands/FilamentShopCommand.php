<?php

namespace Adultdate\FilamentShop\Commands;

use Illuminate\Console\Command;

class FilamentShopCommand extends Command
{
    public $signature = 'filament-shop';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
