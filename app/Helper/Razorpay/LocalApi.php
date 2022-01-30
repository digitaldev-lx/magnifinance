<?php

namespace App\Helper\Razorpay;

use Razorpay\Api\Api;

class LocalApi extends Api
{

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $className = __NAMESPACE__.'\\'.ucwords($name);

        $entity = new $className();

        return $entity;
    }

}
