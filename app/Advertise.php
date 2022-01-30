<?php

namespace App;

use App\Scopes\CompanyScope;
use App\Services\UrlManager;
use Illuminate\Database\Eloquent\Model;

class Advertise extends Model
{
    protected $guarded = [];

    protected $dates = ['paid_on'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);

    }

    protected $appends = [
        'advertise_image_url',
        'formated_price',
        'formated_amount_to_pay',
    ];

    public function getAdvertiseImageUrlAttribute()
    {
        if (is_null($this->image) || $this->image == '') {
            return asset('img/no-image.jpg');
        }
//
        return asset($this->image);
    }

    public function getConvertedAmountToPayAttribute()
    {
        return currencyConvertedPrice($this->company_id, $this->amount);
    }

    public function getFormatedAmountToPayAttribute()
    {
        return currencyFormatter($this->converted_amount_to_pay);
    }

    public function getConvertedAvgAmountToPayAttribute()
    {
        return currencyConvertedPrice($this->attributes['company_id'], $this->avg_amount);
    }

    public function getFormatedAvgAmountToPayAttribute()
    {
        return currencyFormatter($this->converted_avg_amount_to_pay);
    }

    public function getFormatedPriceAttribute()
    {
        return currencyFormatter($this->price);
    }

    public function getFormatedAmountAttribute()
    {
        return currencyFormatter($this->amount);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'completed');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withoutGlobalScopes();
    }

    public function article()
    {
        return $this->belongsTo(Article::class)->withoutGlobalScopes();
    }

}
