<?php

namespace App;

use DateTime;
use Carbon\Carbon;
use App\Scopes\CompanyScope;
use App\Observers\BookingObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Booking
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $coupon_id
 * @property int|null $user_id
 * @property int|null $total_bookings
 * @property int|null $total
 * @property string|int|null $date
 * @property string|int|null $month
 * @property int|null $booking_source
 * @property int|null $countSource
 * @property int $location_id
 * @property int|null $currency_id
 * @property \Illuminate\Support\Carbon $date_time
 * @property string $status
 * @property string $payment_gateway
 * @property float|null $original_amount
 * @property float|null $product_amount
 * @property float $discount
 * @property float $discount_percent
 * @property float|null $coupon_discount
 * @property string $tax_name
 * @property float $tax_percent
 * @property float $tax_amount
 * @property float $amount_to_pay
 * @property string $payment_status
 * @property string $source
 * @property string|null $order_id
 * @property string|null $additional_notes
 * @property string|null $event_id
 * @property string|null $notify_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Company|null $company
 * @property-read \App\Payment|null $completedPayment
 * @property-read \App\Coupon|null $coupon
 * @property-read \App\Currency|null $currency
 * @property-read \App\Deal $deal
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $employees
 * @property-read int|null $employees_count
 * @property-read mixed $converted_amount_to_pay
 * @property-read mixed $converted_coupon_discount
 * @property-read mixed $converted_discount
 * @property-read mixed $converted_original_amount
 * @property-read mixed $converted_product_amount
 * @property-read mixed $converted_tax_amount
 * @property-read mixed $formated_amount_to_pay
 * @property-read mixed $formated_coupon_discount
 * @property-read mixed $formated_discount
 * @property-read mixed $formated_original_amount
 * @property-read mixed $formated_product_amount
 * @property-read mixed $formated_tax_amount
 * @property-read mixed $utc_date_time
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookingItem[] $items
 * @property-read int|null $items_count
 * @property-read \App\Payment|null $payment
 * @property-read \App\Product $product
 * @property-read \App\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Booking newModelQuery()
 * @method static Builder|Booking newQuery()
 * @method static Builder|Booking query()
 * @method static Builder|Booking whereAdditionalNotes($value)
 * @method static Builder|Booking whereAmountToPay($value)
 * @method static Builder|Booking whereCompanyId($value)
 * @method static Builder|Booking whereCouponDiscount($value)
 * @method static Builder|Booking whereCouponId($value)
 * @method static Builder|Booking whereCreatedAt($value)
 * @method static Builder|Booking whereCurrencyId($value)
 * @method static Builder|Booking whereDateTime($value)
 * @method static Builder|Booking whereDiscount($value)
 * @method static Builder|Booking whereDiscountPercent($value)
 * @method static Builder|Booking whereEventId($value)
 * @method static Builder|Booking whereId($value)
 * @method static Builder|Booking whereLocationId($value)
 * @method static Builder|Booking whereNotifyAt($value)
 * @method static Builder|Booking whereOrderId($value)
 * @method static Builder|Booking whereOriginalAmount($value)
 * @method static Builder|Booking wherePaymentGateway($value)
 * @method static Builder|Booking wherePaymentStatus($value)
 * @method static Builder|Booking whereProductAmount($value)
 * @method static Builder|Booking whereSource($value)
 * @method static Builder|Booking whereStatus($value)
 * @method static Builder|Booking whereTaxAmount($value)
 * @method static Builder|Booking whereTaxName($value)
 * @method static Builder|Booking whereTaxPercent($value)
 * @method static Builder|Booking whereUpdatedAt($value)
 * @method static Builder|Booking whereUserId($value)
 * @mixin \Eloquent
 */
class Booking extends Model
{
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
        return $this->hasMany(BookingItem::class);
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
