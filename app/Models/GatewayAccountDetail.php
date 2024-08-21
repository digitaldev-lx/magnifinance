<?php

namespace App\Models;

use App\Observers\GatewayAccountDetailObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Stripe\Stripe;

class GatewayAccountDetail extends Model
{
    protected $guarded = ['id'];

    protected $dates = [
        'link_expire_at'
    ];

    public $appends = [ 'stripe_login_link' ];

    protected static function boot()
    {
        parent::boot();

        static::observe(GatewayAccountDetailObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

    public function getStripeLoginLinkAttribute() {
        $stripeCredentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();
        $stripePaymentSetting = GatewayAccountDetail::ofStatus('active')->ofGateway('stripe')->first();
        /** setup Stripe credentials **/
        Stripe::setApiKey($stripeCredentials->stripe_secret);
        return \Stripe\Account::createLoginLink($stripePaymentSetting->account_id);
    }

    public function company()
    {
        $this->belongsTo(Company::class);
    }

    public function scopeActiveConnectedOfGateway($query, $type)
    {
        return $query->whereAccountStatus('active')->whereConnectionStatus('connected')->whereGateway($type);
    }

    public function scopeOfStatus($query, $type)
    {
        return $query->whereAccountStatus($type);
    }

    public function scopeOfConnectionType($query, $type)
    {
        return $query->whereConnectionStatus($type);
    }

    public function scopeOfGateway($query, $type)
    {
        return $query->whereGateway($type);
    }

}


