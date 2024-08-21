<?php

namespace App\Models;

use App\Observers\OfflineInvoiceObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
