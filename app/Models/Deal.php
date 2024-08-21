<?php

namespace App\Models;

use App\Observers\DealObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Shetabit\Visitor\Traits\Visitable;

class Deal extends Model
{

    use Visitable;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        static::observe(DealObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    protected $appends = [
        'deal_image_url',
        'applied_between_time',
        'deal_detail_url',
        'converted_original_amount',
        'converted_deal_amount',
        'formated_original_amount',
        'formated_deal_amount',
    ];

    // Relations

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function services()
    {
        return $this->hasMany(DealItem::class);
    }

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'deal_id', 'id');
    }

    public function dealTaxes()
    {
        return $this->hasMany(ItemTax::class, 'deal_id', 'id');
    }

    public function spotlight()
    {
        return $this->hasMany(Spotlight::class, 'deal_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Scopes

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

    // Accessors

    public function getDealImageUrlAttribute()
    {
        if(is_null($this->image)){
            return "https://media.istockphoto.com/photos/stylish-shopaholic-with-purchases-picture-id1169378197?k=20&m=1169378197&s=612x612&w=0&h=QuZ4laEcaxPzCVOt57C8cDiDgZPGU_9LrVkZ2OjPEsY=";
        }

        return cdn_storage_url($this->image);
    }

    public function getAppliedBetweenTimeAttribute()
    {
        return $this->open_time.' - '.$this->close_time;
    }

    public function getStartDateAttribute($value)
    {
        $date = new Carbon($value);
        return $date->format('Y-m-d h:i A');
    }

    public function getEndDateAttribute($value)
    {
        $date = new Carbon($value);
        return $date->format('Y-m-d h:i A');
    }

    public function getOpenTimeAttribute($value)
    {
        return Carbon::createFromFormat('H:i:s', $value)->setTimezone($this->company->timezone)->format($this->company->time_format);
    }

    public function getCloseTimeAttribute($value)
    {
        return Carbon::createFromFormat('H:i:s', $value)->setTimezone($this->company->timezone)->format($this->company->time_format);
    }

    public function getmaxOrderPerCustomerAttribute($value)
    {
        if($this->uses_limit == 0 && $value == 0) {
            return 'Infinite';
        }
        elseif($this->uses_limit > 0 && ($value == 0 || $value == '')) {
            return $this->uses_limit;
        }
        return $value;
    }

    public function getTotalTaxPercentAttribute()
    {
        if (!$this->dealTaxes) {
            return 0;
        }

        $taxPercent = 0;

        foreach ($this->dealTaxes as $key => $tax) {
            $taxPercent += $tax->tax->percent;
        }

        return $taxPercent;
    }

    public function getDealDetailUrlAttribute()
    {
        return route('front.dealDetail', ['dealSlug' => $this->slug]);
    }

    public function getConvertedOriginalAmountAttribute()
    {
        return currencyConvertedPrice($this->company_id, $this->original_amount);
    }

    public function getConvertedDealAmountAttribute()
    {
        return currencyConvertedPrice($this->company_id, $this->deal_amount);
    }

    public function getFormatedOriginalAmountAttribute()
    {
        return currencyFormatter($this->converted_original_amount);
    }

    public function getFormatedDealAmountAttribute()
    {
        return currencyFormatter($this->converted_deal_amount);
    }

} /* end of class */
