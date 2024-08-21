<?php

namespace App\Observers;

use App\Models\GatewayAccountDetail;

class GatewayAccountDetailObserver
{

    public function creating(GatewayAccountDetail $gatewayAccountDetail)
    {
        if (company()) {
            $gatewayAccountDetail->company_id = company()->id;
        }
    }

}
