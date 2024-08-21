<?php

namespace App\Models;

use App\Observers\CurrencyObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::observe(CurrencyObserver::class);

    }

    protected $appends = [ 'has_companies'];

    protected $guarded = ['id'];

    public function getExchangeRateAttribute($value)
    {
        return $value ?? 1;
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 'currency_id', 'id');
    }

    public function getHasCompaniesAttribute()
    {
        return $this->companies->count() > 0;
    }

}
