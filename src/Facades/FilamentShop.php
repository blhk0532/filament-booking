<?php

namespace Adultdate\FilamentShop\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Adultdate\FilamentShop\FilamentShop
 */
class FilamentShop extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Adultdate\FilamentShop\FilamentShop::class;
    }
}
