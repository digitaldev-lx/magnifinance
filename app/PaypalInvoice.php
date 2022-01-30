<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PaypalInvoice
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $currency_id
 * @property float|null $sub_total
 * @property float|null $total
 * @property string|null $transaction_id
 * @property string|null $remarks
 * @property string|null $billing_frequency
 * @property int|null $billing_interval
 * @property \Illuminate\Support\Carbon|null $paid_on
 * @property \Illuminate\Support\Carbon|null $next_pay_date
 * @property string|null $recurring
 * @property string|null $status
 * @property string|null $plan_id
 * @property string|null $event_id
 * @property string|null $end_on
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $package_id
 * @property-read \App\Company|null $company
 * @property-read \App\Currency|null $currency
 * @property-read \App\Package|null $package
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereBillingFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereBillingInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereEndOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereNextPayDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice wherePaidOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereRecurring($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaypalInvoice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PaypalInvoice extends Model
{
    protected $table = 'paypal_invoices';
    protected $dates = ['paid_on', 'next_pay_date'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->withoutGlobalScopes(['active']);
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

}
