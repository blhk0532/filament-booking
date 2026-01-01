<?php

namespace Adultdate\FilamentShop\Filament\Clusters\Services;

use BackedEnum;
use Filament\Clusters\Cluster;
use UnitEnum;

class ServicesCluster extends Cluster
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string | UnitEnum | null $navigationGroup = 'Services';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'shop/services';
}