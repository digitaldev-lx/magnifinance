<?php

namespace App\Observers;

use App\Coupon;

class CouponObserver
{

    public function saving(Coupon $coupon)
    {
        $coupon->title = strtolower($coupon->title);
        $coupon->code = strtolower($coupon->code);

        if ($coupon->isDirty('days')){
            $coupon->days = json_encode($coupon->days);
        }
    }

}
