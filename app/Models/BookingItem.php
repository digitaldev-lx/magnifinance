<?php

namespace App\Models;

use App\Observers\BookingItemObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\BookingItem
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $deal_id
 * @property int $booking_id
 * @property int|null $business_service_id
 * @property int|null $product_id
 * @property int $quantity
 * @property float $unit_price
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking $booking
 * @property-read \App\Models\BusinessService|null $businessService
 * @property-read \App\Models\Deal|null $deal
 * @property-read mixed $converted_unit_price
 * @property-read mixed $formated_unit_price
 * @property-read \App\Models\Product|null $product
 * @method static Builder|BookingItem newModelQuery()
 * @method static Builder|BookingItem newQuery()
 * @method static Builder|BookingItem query()
 * @method static Builder|BookingItem whereAmount($value)
 * @method static Builder|BookingItem whereBookingId($value)
 * @method static Builder|BookingItem whereBusinessServiceId($value)
 * @method static Builder|BookingItem whereCompanyId($value)
 * @method static Builder|BookingItem whereCreatedAt($value)
 * @method static Builder|BookingItem whereDealId($value)
 * @method static Builder|BookingItem whereId($value)
 * @method static Builder|BookingItem whereProductId($value)
 * @method static Builder|BookingItem whereQuantity($value)
 * @method static Builder|BookingItem whereUnitPrice($value)
 * @method static Builder|BookingItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BookingItem extends Model
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::observe(BookingItemObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function businessService()
    {
        return $this->belongsTo(BusinessService::class)->withoutGlobalScopes();
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function ratingByUser()
    {
        return $this->hasOne(Rating::class, 'service_id', 'business_service_id')->where('booking_id', $this->booking_id)->where('user_id', Auth::user()->id);
    }

    public function getConvertedUnitPriceAttribute()
    {
        return cache()->remember('converted_Unit_Price_'. $this->company_id, 60*60, function () {
            return currencyConvertedPrice($this->company_id, $this->unit_price);
        });
    }

    public function getFormatedUnitPriceAttribute()
    {
        return currencyFormatter($this->converted_unit_price);
    }

}
