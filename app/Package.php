<?php

namespace App;

use App\Observers\PackageObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Subscription;

/**
 * App\Package
 *
 * @property int $id
 * @property int|null $currency_id
 * @property string $name
 * @property int $max_employees
 * @property int $max_services
 * @property int $max_deals
 * @property int $max_roles
 * @property int $no_of_days
 * @property int $notify_before_days
 * @property string $trial_message
 * @property float $monthly_price
 * @property float $annual_price
 * @property string|null $stripe_monthly_plan_id
 * @property string|null $stripe_annual_plan_id
 * @property string|null $razorpay_monthly_plan_id
 * @property string|null $razorpay_annual_plan_id
 * @property string|null $package_modules
 * @property string|null $description
 * @property string|null $type
 * @property string $make_private
 * @property string $mark_recommended
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Company|null $company
 * @property-read \App\Currency|null $currency
 * @property-read \Illuminate\Database\Eloquent\Collection|Subscription[] $subscription
 * @property-read int|null $subscription_count
 * @method static Builder|Package active()
 * @method static Builder|Package defaultPackage()
 * @method static Builder|Package newModelQuery()
 * @method static Builder|Package newQuery()
 * @method static Builder|Package query()
 * @method static Builder|Package trialPackage()
 * @method static Builder|Package whereAnnualPrice($value)
 * @method static Builder|Package whereCreatedAt($value)
 * @method static Builder|Package whereCurrencyId($value)
 * @method static Builder|Package whereDescription($value)
 * @method static Builder|Package whereId($value)
 * @method static Builder|Package whereMakePrivate($value)
 * @method static Builder|Package whereMarkRecommended($value)
 * @method static Builder|Package whereMaxDeals($value)
 * @method static Builder|Package whereMaxEmployees($value)
 * @method static Builder|Package whereMaxRoles($value)
 * @method static Builder|Package whereMaxServices($value)
 * @method static Builder|Package whereMonthlyPrice($value)
 * @method static Builder|Package whereName($value)
 * @method static Builder|Package whereNoOfDays($value)
 * @method static Builder|Package whereNotifyBeforeDays($value)
 * @method static Builder|Package wherePackageModules($value)
 * @method static Builder|Package whereRazorpayAnnualPlanId($value)
 * @method static Builder|Package whereRazorpayMonthlyPlanId($value)
 * @method static Builder|Package whereStatus($value)
 * @method static Builder|Package whereStripeAnnualPlanId($value)
 * @method static Builder|Package whereStripeMonthlyPlanId($value)
 * @method static Builder|Package whereTrialMessage($value)
 * @method static Builder|Package whereType($value)
 * @method static Builder|Package whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Package extends Model
{
    protected $fillable = [
        'name',
        'max_employees',
        'max_services',
        'max_deals',
        'max_roles',
        'monthly_price',
        'annual_price',
        'stripe_monthly_plan_id',
        'stripe_annual_plan_id',
        'razorpay_monthly_plan_id',
        'razorpay_annual_plan_id',
        'make_private',
        'mark_recommended',
        'status',
        'package_modules'
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(PackageObserver::class);
    }

    public function scopeTrialPackage()
    {
        return $this->where('type', 'trial');
    }

    public function scopeDefaultPackage()
    {
        return $this->where('type', 'default');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function subscription()
    {
        return $this->hasMany(Subscription::class);
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'package_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

}
