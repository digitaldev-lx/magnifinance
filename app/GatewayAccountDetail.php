<?php

namespace App;

use Carbon\Carbon;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Observers\GatewayAccountDetailObserver;
use Stripe\Stripe;

/**
 * App\GatewayAccountDetail
 *
 * @property int $id
 * @property int $company_id
 * @property string $account_id
 * @property string $connection_status
 * @property string $account_status
 * @property string $gateway
 * @property string|null $link
 * @property \Illuminate\Support\Carbon|null $link_expire_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|GatewayAccountDetail activeConnectedOfGateway($type)
 * @method static Builder|GatewayAccountDetail newModelQuery()
 * @method static Builder|GatewayAccountDetail newQuery()
 * @method static Builder|GatewayAccountDetail ofConnectionType($type)
 * @method static Builder|GatewayAccountDetail ofGateway($type)
 * @method static Builder|GatewayAccountDetail ofStatus($type)
 * @method static Builder|GatewayAccountDetail query()
 * @method static Builder|GatewayAccountDetail whereAccountId($value)
 * @method static Builder|GatewayAccountDetail whereAccountStatus($value)
 * @method static Builder|GatewayAccountDetail whereCompanyId($value)
 * @method static Builder|GatewayAccountDetail whereConnectionStatus($value)
 * @method static Builder|GatewayAccountDetail whereCreatedAt($value)
 * @method static Builder|GatewayAccountDetail whereGateway($value)
 * @method static Builder|GatewayAccountDetail whereId($value)
 * @method static Builder|GatewayAccountDetail whereLink($value)
 * @method static Builder|GatewayAccountDetail whereLinkExpireAt($value)
 * @method static Builder|GatewayAccountDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GatewayAccountDetail extends Model
{
    protected $guarded = ['id'];

    protected $dates = [
        'link_expire_at'
    ];

    public $appends = [ 'stripe_login_link' ];

    protected static function boot()
    {
        parent::boot();

        static::observe(GatewayAccountDetailObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

    public function getStripeLoginLinkAttribute() {
        $stripeCredentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();
        $stripePaymentSetting = GatewayAccountDetail::ofStatus('active')->ofGateway('stripe')->first();
        /** setup Stripe credentials **/
        Stripe::setApiKey($stripeCredentials->stripe_secret);
        return \Stripe\Account::createLoginLink($stripePaymentSetting->account_id);
    }

    public function company()
    {
        $this->belongsTo(Company::class);
    }

    public function scopeActiveConnectedOfGateway($query, $type)
    {
        return $query->whereAccountStatus('active')->whereConnectionStatus('connected')->whereGateway($type);
    }

    public function scopeOfStatus($query, $type)
    {
        return $query->whereAccountStatus($type);
    }

    public function scopeOfConnectionType($query, $type)
    {
        return $query->whereConnectionStatus($type);
    }

    public function scopeOfGateway($query, $type)
    {
        return $query->whereGateway($type);
    }

}


