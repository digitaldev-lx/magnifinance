<?php

namespace DigitalDevLX\Magnifinance;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DigitalDevLX\Magnifinance\Skeleton\SkeletonClass
 */
class MagnifinanceFacade extends Facade
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
