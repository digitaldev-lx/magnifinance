<?php

namespace App\Models;

use App\Observers\PackageObserver;
use App\Traits\Documentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Subscription;

class Package extends Model
{
    use Documentable;

    protected $fillable = [
        'name',
        'max_employees',
        'max_services',
        'max_deals',
        'max_roles',
        'monthly_price',
        'description',
        'annual_price',
        'stripe_monthly_plan_id',
        'stripe_annual_plan_id',
        'razorpay_monthly_plan_id',
        'razorpay_annual_plan_id',
        'make_private',
        'mark_recommended',
        'status',
        'package_modules'
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(PackageObserver::class);
    }

    public function scopeTrialPackage()
    {
        return $this->where('type', 'trial');
    }

    public function scopeDefaultPackage()
    {
        return $this->where('type', 'default');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function subscription()
    {
        return $this->hasMany(Subscription::class);
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'package_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

}
