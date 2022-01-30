<?php

namespace DigitaldevLx\MoloniApi;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DigitaldevLx\MoloniApi\Skeleton\SkeletonClass
 */
class MoloniApiFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'moloni-api';
    }
}
