<?php

namespace App\Models;

use App\Observers\LocationObserver;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name','country_id'];

    protected static function boot()
    {
        parent::boot();
        static::observe(LocationObserver::class);
    }

    public function services()
    {
        return $this->hasMany(BusinessService::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

} /* end of class */
