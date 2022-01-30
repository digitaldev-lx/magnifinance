<?php

namespace App;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function service()
    {
        return $this->belongsTo(BusinessService::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
