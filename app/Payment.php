<?php

namespace App;

use Carbon\Carbon;
use App\Scopes\CompanyScope;
use App\Observers\PaymentObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Payment
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $currency_id
 * @property int $booking_id
 * @property int|null $customer_id
 * @property float $amount
 * @property float $total
 * @property float $total_amount
 * @property mixed $date
 * @property mixed $month
 * @property float $commission
 * @property string|null $gateway
 * @property string|null $transaction_id
 * @property string $status
 * @property string|null $transfer_status
 * @property \Illuminate\Support\Carbon|null $paid_on
 * @property string|null $event_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $offline_method_id
 * @property-read \App\Booking $booking
 * @property-read \App\Company|null $company
 * @property-read \App\Currency|null $currency
 * @method static Builder|Payment newModelQuery()
 * @method static Builder|Payment newQuery()
 * @method static Builder|Payment query()
 * @method static Builder|Payment whereAmount($value)
 * @method static Builder|Payment whereBookingId($value)
 * @method static Builder|Payment whereCommission($value)
 * @method static Builder|Payment whereCompanyId($value)
 * @method static Builder|Payment whereCreatedAt($value)
 * @method static Builder|Payment whereCurrencyId($value)
 * @method static Builder|Payment whereCustomerId($value)
 * @method static Builder|Payment whereEventId($value)
 * @method static Builder|Payment whereGateway($value)
 * @method static Builder|Payment whereId($value)
 * @method static Builder|Payment whereOfflineMethodId($value)
 * @method static Builder|Payment wherePaidOn($value)
 * @method static Builder|Payment whereStatus($value)
 * @method static Builder|Payment whereTransactionId($value)
 * @method static Builder|Payment whereTransferStatus($value)
 * @method static Builder|Payment whereUpdatedAt($value)
 * @mixin \Eloquent
 */

class Payment extends Model
{
    protected $dates = ['paid_on'];

    protected static function boot()
    {
        parent::boot();

        static::observe(PaymentObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

}
