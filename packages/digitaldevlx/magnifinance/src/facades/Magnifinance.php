<?php

namespace DigitalDevLX\Magnifinance\facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DigitalDevLX\Magnifinance\Skeleton\SkeletonClass
 */
class Magnifinance extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'magnifinance';
    }
}
