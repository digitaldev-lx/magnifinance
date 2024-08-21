<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentGatewayCredentials extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['show_pay'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getShowPayAttribute()
    {
        return $this->attributes['paypal_status'] == 'active' || $this->attributes['stripe_status'] == 'active' || $this->attributes['razorpay_status'] == 'active';
    }

}
