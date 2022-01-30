<?php
/**
 * Created by PhpStorm.
 * User: DEXTER
 * Date: 24/05/17
 * Time: 11:29 PM
 */

namespace App\Traits;

use App\PaymentGatewayCredentials;
use Illuminate\Support\Facades\Config;

trait StripeSettings
{

    public function setStripConfigs()
    {
        $settings  = PaymentGatewayCredentials::withoutGlobalScopes(['company'])->first();
        $key       = ($settings->stripe_client_id) ? $settings->stripe_client_id : env('STRIPE_KEY');
        $apiSecret = ($settings->stripe_secret) ? $settings->stripe_secret : env('STRIPE_SECRET');
        $webhookKey = ($settings->stripe_webhook_secret) ? $settings->stripe_webhook_secret : env('STRIPE_WEBHOOK_SECRET');

        Config::set('cashier.key', $key);
        Config::set('cashier.secret', $apiSecret);
        Config::set('cashier.webhook.secret', $webhookKey);
    }

}



