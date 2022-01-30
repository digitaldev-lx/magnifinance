<?php

namespace App;

use App\Observers\CouponObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Coupon
 *
 * @property int $id
 * @property string $title
 * @property string|null $code
 * @property \Illuminate\Support\Carbon|null $start_date_time
 * @property \Illuminate\Support\Carbon|null $end_date_time
 * @property int|null $uses_limit
 * @property int|null $used_time
 * @property float|null $amount
 * @property string|null $discount_type
 * @property int $minimum_purchase_amount
 * @property string|null $days
 * @property string $status
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CouponUser[] $customers
 * @property-read int|null $customers_count
 * @method static Builder|Coupon active()
 * @method static Builder|Coupon newModelQuery()
 * @method static Builder|Coupon newQuery()
 * @method static Builder|Coupon query()
 * @method static Builder|Coupon whereAmount($value)
 * @method static Builder|Coupon whereCode($value)
 * @method static Builder|Coupon whereCreatedAt($value)
 * @method static Builder|Coupon whereDays($value)
 * @method static Builder|Coupon whereDescription($value)
 * @method static Builder|Coupon whereDiscountType($value)
 * @method static Builder|Coupon whereEndDateTime($value)
 * @method static Builder|Coupon whereId($value)
 * @method static Builder|Coupon whereMinimumPurchaseAmount($value)
 * @method static Builder|Coupon whereStartDateTime($value)
 * @method static Builder|Coupon whereStatus($value)
 * @method static Builder|Coupon whereTitle($value)
 * @method static Builder|Coupon whereUpdatedAt($value)
 * @method static Builder|Coupon whereUsedTime($value)
 * @method static Builder|Coupon whereUsesLimit($value)
 * @mixin \Eloquent
 */
class Coupon extends Model
{
    protected $fillable = [
        'title',
        'code',
        'start_date_time',
        'uses_limit',
        'amount',
        'discount_type',
        'minimum_purchase_amount',
        'days',
        'description',
        'status',
        'end_date_time'
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(CouponObserver::class);
    }

    protected $dates = ['start_date_time', 'end_date_time', 'created_at'];

    public function customers()
    {
        return $this->hasMany(CouponUser::class, 'coupon_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

}
