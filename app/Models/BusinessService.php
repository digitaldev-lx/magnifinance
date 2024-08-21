<?php

namespace App\Models;

use App\Observers\BusinessServiceObserver;
use App\Scopes\CompanyScope;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Shetabit\Visitor\Traits\Visitable;

class BusinessService extends Model
{

    use Visitable;

    protected static function boot()
    {
        parent::boot();

        static::observe(BusinessServiceObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    protected $appends = [
        'service_image_url',
        'service_detail_url',
        'converted_price',
        'converted_discounted_price',
        'formated_price',
        'formated_discounted_price',
        'discounted_price',
        'price_with_taxes'
    ];

    public function getServiceImageUrlAttribute()
    {
        if(is_null($this->default_image)){
            return "https://media.istockphoto.com/photos/closeup-portrait-of-her-she-nicelooking-attractive-cheerful-cheery-picture-id1206184490?k=20&m=1206184490&s=612x612&w=0&h=YkCMMkTl_5Q47TYcLjchum1jTzJYmzG7_8kom2DP3lo=";
            return asset('img/default-avatar-user.png');
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

    public function getServiceDetailUrlAttribute()
    {
        return route('front.serviceDetail', ['serviceSlug' => $this->slug, 'companyId' => $this->company_id]);
    }

    public function getDiscountedPriceAttribute()
    {

        if($this->discount > 0){

            if($this->discount_type == 'fixed'){
                return round(($this->price - $this->discount), 2);
            }

            elseif($this->discount_type == 'percent'){

                $discount = (($this->discount / 100) * $this->price);

                return round(($this->price - $discount), 2);
            }
        }

//        return $this->tax_on_price_status !== "active" ? $this->price : $this->net_price;
        return $this->price;
    }



    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeActiveCompany($query)
    {
        return $query->whereHas('company', function($q){
            $q->withoutGlobalScope(CompanyScope::class)->active();
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'service_id', 'id');
    }

    public function taxServices()
    {
        return $this->hasMany(ItemTax::class, 'service_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
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
        return cache()->remember('currencyFormatter', 60*60, function () {
            return currencyFormatter($this->converted_price);
        });
    }

    public function getFormatedDiscountedPriceAttribute()
    {
        return currencyFormatter($this->converted_discounted_price);
    }

    public function getTotalTaxPercentAttribute()
    {
        if (!$this->taxServices) {
            return 0;
        }

        $taxPercent = 0;

        foreach ($this->taxServices as $key => $tax) {
            $taxPercent += $tax->tax->percent;
        }

        return $taxPercent;
    }

    public function getPriceWithTaxesAttribute()
    {
        $taxServices = cache()->remember('taxServices', 60*60, function () {
            return $this->taxServices;
        });
        if (!$taxServices) {
            return 0;
        }

        $taxPercent = 0;

        foreach ($taxServices as $key => $tax) {
            $taxPercent += $tax->tax->percent;
        }

        /*if($this->tax_on_price_status == "active"){
            return $this->net_price + $this->net_price * ($taxPercent / 100);
        }else{
            return $this->price + $this->price * ($taxPercent / 100);
        }*/
        return $this->price + $this->price * ($taxPercent / 100);

    }

}
