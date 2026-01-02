<?php

namespace Adultdate\FilamentBooking\Contracts;

use Adultdate\FilamentBooking\Enums\Context;

interface ContextualInfo
{
    public function getContext(): Context;
}
