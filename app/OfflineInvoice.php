<?php

namespace App;

use App\Observers\OfflineInvoiceObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\OfflineInvoice
 *
 * @property int $id
 * @property int $company_id
 * @property int $package_id
 * @property int|null $offline_method_id
 * @property string|null $transaction_id
 * @property string $amount
 * @property \Illuminate\Support\Carbon $pay_date
 * @property \Illuminate\Support\Carbon|null $next_pay_date
 * @property string $status
 * @property string|null $package_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Company $company
 * @property-read \App\OfflinePaymentMethod|null $offlinePaymentMethod
 * @property-read \App\OfflinePlanChange|null $offlinePlanChangeRequest
 * @property-read \App\Package $package
 * @method static Builder|OfflineInvoice newModelQuery()
 * @method static Builder|OfflineInvoice newQuery()
 * @method static Builder|OfflineInvoice query()
 * @method static Builder|OfflineInvoice whereAmount($value)
 * @method static Builder|OfflineInvoice whereCompanyId($value)
 * @method static Builder|OfflineInvoice whereCreatedAt($value)
 * @method static Builder|OfflineInvoice whereId($value)
 * @method static Builder|OfflineInvoice whereNextPayDate($value)
 * @method static Builder|OfflineInvoice whereOfflineMethodId($value)
 * @method static Builder|OfflineInvoice wherePackageId($value)
 * @method static Builder|OfflineInvoice wherePackageType($value)
 * @method static Builder|OfflineInvoice wherePayDate($value)
 * @method static Builder|OfflineInvoice whereStatus($value)
 * @method static Builder|OfflineInvoice whereTransactionId($value)
 * @method static Builder|OfflineInvoice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OfflineInvoice extends Model
{
    protected $guarded = ['id'];

    protected $dates = [
        'pay_date',
        'next_pay_date'
    ];

    protected $globalDateFormat;

    protected static function boot()
    {
        parent::boot();

        static::observe(OfflineInvoiceObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function __construct()
    {
        parent::__construct();

        $this->globalDateFormat = GlobalSetting::select('id', 'date_format')->first()->date_format;
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->withoutGlobalScopes(['active']);
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function offlinePaymentMethod()
    {
        return $this->belongsTo(OfflinePaymentMethod::class, 'offline_method_id');
    }

    public function offlinePlanChangeRequest()
    {
        return $this->hasOne(OfflinePlanChange::class, 'invoice_id');
    }

    public function setPayDateAttribute($value)
    {
        return $this->attributes['pay_date'] = Carbon::createFromFormat($this->globalDateFormat, $value)->format('Y-m-d');
    }

    public function setNextPayDateAttribute($value)
    {
        return $this->attributes['next_pay_date'] = Carbon::createFromFormat($this->globalDateFormat, $value)->format('Y-m-d');
    }

}
