<?php

namespace App\Models;

use App\Observers\PaymentObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $dates = ['paid_on'];

    protected static function boot()
    {
        parent::boot();

        static::observe(PaymentObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

}
