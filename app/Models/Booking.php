<?php

namespace App\Models;

use App\Observers\BookingObserver;
use App\Scopes\CompanyScope;
use App\Traits\Documentable;
use App\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use Documentable;

    protected $dates = ['date_time'];
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::observe(BookingObserver::class);

        static::addGlobalScope(new CompanyScope);

    }

    public function user()
    {
        return $this->belongsTo(User::class)->withoutGlobalScopes();
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function employees()
    {
        return $this->belongsToMany(User::class, 'employee_id');
    }

    public function completedPayment()
    {
        return $this->hasOne(Payment::class)->where('status', 'completed')->whereNotNull('paid_on');
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class, "booking_id", "id")->withoutGlobalScopes();
    }

    public function payment()
    {
        return $this->hasOne(Payment::class)->where('status', 'completed')->whereNotNull('paid_on');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function setDateTimeAttribute($value)
    {
        $this->attributes['date_time'] = Carbon::parse($value, Company::first()->timezone)->setTimezone('UTC');
    }

    public function getDateTimeAttribute($value)
    {
        if ($this->validateDate($value)) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $value)->setTimezone(Company::first()->timezone);
        }

        return '';
    }

    public function getUtcDateTimeAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['date_time']);
    }

    // Validations

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'booking_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function getConvertedOriginalAmountAttribute()
    {
        return currencyConvertedPrice($this->company_id, $this->original_amount);
    }

    public function getConvertedProductAmountAttribute()
    {
        return currencyConvertedPrice($this->company_id, $this->product_amount);
    }

    public function getConvertedDiscountAttribute()
    {
        return currencyConvertedPrice($this->company_id, $this->discount);
    }

    public function getFormatedPrePaymentDiscountAttribute()
    {
        return currencyFormatter($this->converted_pre_payment_discount);
    }

    public function getConvertedPrePaymentDiscountAttribute()
    {
        $prePaymentdiscount = $this->amount_to_pay * ($this->prepayment_discount_percent / 100);
        return currencyConvertedPrice($this->company_id, $prePaymentdiscount);
    }

    public function getConvertedCouponDiscountAttribute()
    {
        return currencyConvertedPrice($this->company_id, $this->coupon_discount);
    }

    public function getConvertedTaxAmountAttribute()
    {
        return currencyConvertedPrice($this->company_id, $this->tax_amount);
    }

    public function getConvertedAmountToPayAttribute()
    {
        return currencyConvertedPrice($this->company_id, $this->amount_to_pay);
    }

    public function getFormatedOriginalAmountAttribute()
    {
        return currencyFormatter($this->converted_original_amount);
    }

    public function getFormatedProductAmountAttribute()
    {
        return currencyFormatter($this->converted_product_amount);
    }

    public function getFormatedDiscountAttribute()
    {
        return currencyFormatter($this->converted_discount);
    }

    public function getFormatedCouponDiscountAttribute()
    {
        return currencyFormatter($this->converted_coupon_discount);
    }

    public function getFormatedTaxAmountAttribute()
    {
        return currencyFormatter($this->converted_tax_amount);
    }

    public function getFormatedAmountToPayAttribute()
    {
        return currencyFormatter($this->converted_amount_to_pay);
    }

} /* end of class */
