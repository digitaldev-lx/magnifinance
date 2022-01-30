<?php

namespace App;

use App\Observers\OfflinePlanChangeObserver;
use Illuminate\Database\Eloquent\Model;

/**
 * App\OfflinePlanChange
 *
 * @property int $id
 * @property int $company_id
 * @property int $package_id
 * @property string $package_type
 * @property int $invoice_id
 * @property int $offline_method_id
 * @property string|null $file_name
 * @property string $status
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Company $company
 * @property-read mixed $file
 * @property-read \App\OfflinePaymentMethod $offlineMethod
 * @property-read \App\Package $package
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange query()
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange whereOfflineMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange wherePackageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfflinePlanChange whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OfflinePlanChange extends Model
{
    protected $appends = ['file'];

    protected static function boot()
    {
        parent::boot();
        static::observe(OfflinePlanChangeObserver::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function offlineMethod()
    {
        return $this->belongsTo(OfflinePaymentMethod::class, 'offline_method_id');
    }

    public function getFileAttribute()
    {
        return ($this->file_name) ? asset_url('offline-payment-files/' . $this->file_name) : asset('img/default-profile-3.png');
    }

}
