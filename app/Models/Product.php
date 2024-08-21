<?php

namespace App\Models;

use App\Observers\ProductObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Attributes

    protected static function boot()
    {
        parent::boot();
        static::observe(ProductObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

    protected $appends = [
        'product_image_url',
        'converted_price',
        'converted_discounted_price',
        'formated_price',
        'formated_discounted_price',
        'discounted_price'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function productTaxes()
    {
        return $this->hasMany(ItemTax::class, 'product_id', 'id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'product_id', 'id');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors

    public function getProductImageUrlAttribute()
    {
        if(is_null($this->default_image)){
            return "https://media.istockphoto.com/photos/set-of-decorative-cosmetic-picture-id493029628?k=20&m=493029628&s=612x612&w=0&h=5mU5Zh62ALWfhCciWpyHveY2PYw146tQjytYNvC7UFI=";
        }

        return cdn_storage_url($this->default_image);
    }

    public function getImageAttribute($value)
    {
        if (is_array(json_decode($value, true))) {
            return json_decode($value, true);
        }

        return $value;
    }

    public function getDiscountedPriceAttribute()
    {
        if($this->discount > 0){
            if($this->discount_type == 'fixed'){
                return ($this->price - $this->discount);
            }
            elseif($this->discount_type == 'percent'){
                $discount = (($this->discount / 100) * $this->price);
                return round(($this->price - $discount), 2);
            }
        }

        return $this->price;
    }

    public function getTotalTaxPercentAttribute()
    {
        if (!$this->productTaxes) {
            return 0;
        }

        $taxPercent = 0;

        foreach ($this->productTaxes as $key => $tax) {
            $taxPercent += $tax->tax->percent;
        }

        return $taxPercent;
    }

    public function getConvertedPriceAttribute()
    {
        return currencyConvertedPrice($this->company_id, $this->price);
    }

    public function getConvertedDiscountedPriceAttribute()
    {
        return currencyConvertedPrice($this->company_id, $this->discounted_price);
    }

    public function getFormatedPriceAttribute()
    {
        return currencyFormatter($this->converted_price);
    }

    public function getFormatedDiscountedPriceAttribute()
    {
        return currencyFormatter($this->converted_discounted_price);
    }

}
