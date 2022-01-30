<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\StripeInvoice
 *
 * @property int $id
 * @property int $company_id
 * @property string $invoice_id
 * @property string $transaction_id
 * @property string $amount
 * @property \Illuminate\Support\Carbon $pay_date
 * @property \Illuminate\Support\Carbon|null $next_pay_date
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $package_id
 * @property-read \App\Company $company
 * @property-read \App\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice whereNextPayDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice wherePayDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StripeInvoice extends Model
{
    protected $table = 'stripe_invoices';
    protected $dates = ['pay_date', 'next_pay_date'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->withoutGlobalScopes(['active']);
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

}
