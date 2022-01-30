<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RazorpaySubscription
 *
 * @property int $id
 * @property int $company_id
 * @property string|null $subscription_id
 * @property string|null $customer_id
 * @property string $name
 * @property string $razorpay_id
 * @property string $razorpay_plan
 * @property int $quantity
 * @property string|null $trial_ends_at
 * @property string|null $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Company $company
 * @property-read \App\Currency $currency
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription whereRazorpayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription whereRazorpayPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpaySubscription whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RazorpaySubscription extends Model
{
    protected $dates = ['created_at'];

    protected $table = 'razorpay_subscriptions';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

}
