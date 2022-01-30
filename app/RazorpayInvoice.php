<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RazorpayInvoice
 *
 * @property int $id
 * @property int $company_id
 * @property int|null $currency_id
 * @property string $invoice_id
 * @property string $subscription_id
 * @property string|null $order_id
 * @property int $package_id
 * @property string $transaction_id
 * @property string $amount
 * @property \Illuminate\Support\Carbon $pay_date
 * @property \Illuminate\Support\Carbon|null $next_pay_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Company $company
 * @property-read \App\Currency|null $currency
 * @property-read \App\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice whereNextPayDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice wherePayDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RazorpayInvoice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RazorpayInvoice extends Model
{
    protected $table = 'razorpay_invoices';
    protected $dates = ['pay_date', 'next_pay_date'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->withoutGlobalScopes(['active']);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withTrashed();
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

}
