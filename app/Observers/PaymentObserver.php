<?php

namespace App\Observers;

use App\Models\Payment;

class PaymentObserver
{

    public function creating(Payment $payment)
    {
        if (company()) {
            $payment->company_id = company()->id;
        }
    }

}
