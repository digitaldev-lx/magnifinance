<?php

namespace App;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\PaymentGatewayCredentials
 *
 * @property int $id
 * @property int|null $company_id
 * @property string $offline_payment
 * @property string $show_payment_options
 * @property string|null $paypal_client_id
 * @property string|null $paypal_secret
 * @property string $paypal_mode
 * @property string $paypal_status
 * @property string|null $stripe_client_id
 * @property string|null $stripe_secret
 * @property string|null $stripe_webhook_secret
 * @property string $stripe_status
 * @property string $stripe_commission_status
 * @property int|null $stripe_commission_percentage
 * @property string|null $razorpay_key
 * @property string|null $razorpay_secret
 * @property string|null $razorpay_webhook_secret
 * @property string $razorpay_status
 * @property string $razorpay_commission_status
 * @property int|null $razorpay_commission_percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Company|null $company
 * @property-read mixed $show_pay
 * @method static Builder|PaymentGatewayCredentials newModelQuery()
 * @method static Builder|PaymentGatewayCredentials newQuery()
 * @method static Builder|PaymentGatewayCredentials query()
 * @method static Builder|PaymentGatewayCredentials whereCompanyId($value)
 * @method static Builder|PaymentGatewayCredentials whereCreatedAt($value)
 * @method static Builder|PaymentGatewayCredentials whereId($value)
 * @method static Builder|PaymentGatewayCredentials whereOfflinePayment($value)
 * @method static Builder|PaymentGatewayCredentials wherePaypalClientId($value)
 * @method static Builder|PaymentGatewayCredentials wherePaypalMode($value)
 * @method static Builder|PaymentGatewayCredentials wherePaypalSecret($value)
 * @method static Builder|PaymentGatewayCredentials wherePaypalStatus($value)
 * @method static Builder|PaymentGatewayCredentials whereRazorpayCommissionPercentage($value)
 * @method static Builder|PaymentGatewayCredentials whereRazorpayCommissionStatus($value)
 * @method static Builder|PaymentGatewayCredentials whereRazorpayKey($value)
 * @method static Builder|PaymentGatewayCredentials whereRazorpaySecret($value)
 * @method static Builder|PaymentGatewayCredentials whereRazorpayStatus($value)
 * @method static Builder|PaymentGatewayCredentials whereRazorpayWebhookSecret($value)
 * @method static Builder|PaymentGatewayCredentials whereShowPaymentOptions($value)
 * @method static Builder|PaymentGatewayCredentials whereStripeClientId($value)
 * @method static Builder|PaymentGatewayCredentials whereStripeCommissionPercentage($value)
 * @method static Builder|PaymentGatewayCredentials whereStripeCommissionStatus($value)
 * @method static Builder|PaymentGatewayCredentials whereStripeSecret($value)
 * @method static Builder|PaymentGatewayCredentials whereStripeStatus($value)
 * @method static Builder|PaymentGatewayCredentials whereStripeWebhookSecret($value)
 * @method static Builder|PaymentGatewayCredentials whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PaymentGatewayCredentials extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['show_pay'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getShowPayAttribute()
    {
        return $this->attributes['paypal_status'] == 'active' || $this->attributes['stripe_status'] == 'active' || $this->attributes['razorpay_status'] == 'active';
    }

}
