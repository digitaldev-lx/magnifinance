<?php

namespace App;

use App\Observers\OfflineInvoicePaymentObserver;
use Illuminate\Database\Eloquent\Model;

/**
 * App\OfflineInvoicePayment
 *
 * @property int $id
 * @property int|null $invoice_id
 * @property int|null $client_id
 * @property int|null $payment_method_id
 * @property string $slip
 * @property string $description
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\OfflinePaymentMethod|null $paymentMethod
 * @method static \Illuminate\Database\Eloquent\Builder|OfflineInvoicePayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfflineInvoicePayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfflineInvoicePayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|OfflineInvoicePayment whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflineInvoicePayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflineInvoicePayment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflineInvoicePayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflineInvoicePayment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflineInvoicePayment wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflineInvoicePayment whereSlip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflineInvoicePayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflineInvoicePayment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OfflineInvoicePayment extends Model
{

    protected static function boot()
    {
        parent::boot();
        static::observe(OfflineInvoicePaymentObserver::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(OfflinePaymentMethod::class, 'payment_method_id');
    }

    public function getSlipAttribute()
    {
        return ($this->attributes['slip']) ? asset_url('offline-payment-files/' . $this->attributes['slip']) : asset('img/default-profile-3.png');
    }

}
