<?php

namespace App;

use Carbon\Carbon;
use App\Scopes\CompanyScope;
use App\Observers\DealObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Shetabit\Visitor\Traits\Visitable;

/**
 * App\Deal
 *
 * @property int $id
 * @property int|null $company_id
 * @property int $location_id
 * @property string $title
 * @property string|null $slug
 * @property string $deal_type
 * @property string|null $start_date_time
 * @property string|null $end_date_time
 * @property string $open_time
 * @property string $close_time
 * @property int|null $uses_limit
 * @property int|null $used_time
 * @property float|null $original_amount
 * @property float|null $deal_amount
 * @property string|null $days
 * @property string|null $image
 * @property string $status
 * @property string|null $description
 * @property string $discount_type
 * @property int|null $percentage
 * @property string $deal_applied_on
 * @property int|null $max_order_per_customer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookingItem[] $bookingItems
 * @property-read int|null $booking_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Booking[] $bookings
 * @property-read int|null $bookings_count
 * @property-read \App\Company|null $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ItemTax[] $dealTaxes
 * @property-read int|null $deal_taxes_count
 * @property-read mixed $applied_between_time
 * @property-read mixed $converted_deal_amount
 * @property-read mixed $converted_original_amount
 * @property-read mixed $deal_detail_url
 * @property-read mixed $deal_image_url
 * @property-read mixed $end_date
 * @property-read mixed $formated_deal_amount
 * @property-read mixed $formated_original_amount
 * @property-read mixed $start_date
 * @property-read mixed $total_tax_percent
 * @property-read \App\Location $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\DealItem[] $services
 * @property-read int|null $services_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Spotlight[] $spotlight
 * @property-read int|null $spotlight_count
 * @method static Builder|Deal active()
 * @method static Builder|Deal activeCompany()
 * @method static Builder|Deal newModelQuery()
 * @method static Builder|Deal newQuery()
 * @method static Builder|Deal query()
 * @method static Builder|Deal whereCloseTime($value)
 * @method static Builder|Deal whereCompanyId($value)
 * @method static Builder|Deal whereCreatedAt($value)
 * @method static Builder|Deal whereDays($value)
 * @method static Builder|Deal whereDealAmount($value)
 * @method static Builder|Deal whereDealAppliedOn($value)
 * @method static Builder|Deal whereDealType($value)
 * @method static Builder|Deal whereDescription($value)
 * @method static Builder|Deal whereDiscountType($value)
 * @method static Builder|Deal whereEndDateTime($value)
 * @method static Builder|Deal whereId($value)
 * @method static Builder|Deal whereImage($value)
 * @method static Builder|Deal whereLocationId($value)
 * @method static Builder|Deal whereMaxOrderPerCustomer($value)
 * @method static Builder|Deal whereOpenTime($value)
 * @method static Builder|Deal whereOriginalAmount($value)
 * @method static Builder|Deal wherePercentage($value)
 * @method static Builder|Deal whereSlug($value)
 * @method static Builder|Deal whereStartDateTime($value)
 * @method static Builder|Deal whereStatus($value)
 * @method static Builder|Deal whereTitle($value)
 * @method static Builder|Deal whereUpdatedAt($value)
 * @method static Builder|Deal whereUsedTime($value)
 * @method static Builder|Deal whereUsesLimit($value)
 * @mixin \Eloquent
 */
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
