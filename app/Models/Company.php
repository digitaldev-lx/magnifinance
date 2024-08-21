<?php

namespace App\Models;

use App\Observers\CompanyObserver;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class Company extends Model
{
    use Notifiable, Billable, HasSpatial;

    protected $casts = [
        'lat_long' => Point::class,
    ];

    protected $fillable = [
        'company_name',
        'company_email',
        'company_phone',
        'magnifinance_active',
        'partner_token',
        'vat_number',
        'post_code',
        'address',
        "country_id",
        'date_format',
        'time_format',
        'website',
        'timezone',
        'currency_id',
        'locale',
        'lat_long',
        'logo'
    ];

    protected $appends = [
        'logo_url',
        'formatted_phone_number',
        'formatted_address',
        'formatted_website',
        'company_verification_url'
    ];

    protected static function boot()
    {
        parent::boot();

        static::observe(CompanyObserver::class);

        $company = company();

        static::addGlobalScope('company', function (Builder $builder) use ($company) {
            if ($company) {
                $builder->where('id', $company->id);
            }
        });
    }

    public function getLogoUrlAttribute()
    {
        $globalSetting = cache()->remember('GlobalSetting', 60*60, function () {
            return GlobalSetting::first();
        });

        if (is_null($this->logo)) {
            return $globalSetting->logo_url;
        }

        return cdn_storage_url($this->logo);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function deals()
    {
        return $this->belongsToMany(Deal::class);
    }

    public function spotlight()
    {
        return $this->hasMany(Spotlight::class, 'company_id', 'id');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class, 'company_id', 'id');
    }

    public function moduleSetting()
    {
        return $this->hasMany(ModuleSetting::class, 'company_id', 'id');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'company_id', 'id');
    }

    public function owner()
    {
        return $this->hasOne(User::class, 'company_id', 'id');
    }

    public function gatewayAccountDetails()
    {
        return $this->hasMany(GatewayAccountDetail::class);
    }

    public function bookingNotNotify()
    {
        return $this->hasMany(Booking::class)->withoutGlobalScopes()->whereNull('notify_at');
    }

    public function bookingNotification()
    {
        return $this->hasMany(BookingNotification::class);
    }

    public function getCompanyVerificationUrlAttribute()
    {
        return Crypt::encryptString($this->company_email);
    }

    public function getFormattedPhoneNumberAttribute()
    {
        return $this->phoneNumberFormat($this->company_phone);
    }

    public function getFormattedAddressAttribute()
    {
        return nl2br(str_replace('\\r\\n', "\r\n", $this->address));
    }

    public function getFormattedWebsiteAttribute()
    {
        return preg_replace('/^https?:\/\//', '', $this->website);
    }

    public function phoneNumberFormat($number)
    {
        // Allow only Digits, remove all other characters.
        $number = preg_replace('/[^\d]/', '', $number);

        // get number length.
        $length = strlen($number);

        if ($length == 10) {
            if (preg_match('/^1?(\d{3})(\d{3})(\d{4})$/', $number, $matches)) {
                $result = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
                return $result;
            }
        }

        return $number;
    }

    public function setSlugAttribute($value)
    {

        if (static::whereSlug($slug = Str::slug($value))->exists()) {

            $slug = $this->incrementSlug($slug);
        }

        $this->attributes['slug'] = $slug;
    }

    public function incrementSlug($slug)
    {

        $original = $slug;

        $count = 2;

        while (static::whereSlug($slug)->exists()) {

            $slug = $original.'-'. $count++;
        }

        return $slug;

    }

    public function vendorPage()
    {
        return $this->hasOne(VendorPage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCronActive($query)
    {
        return $query->where('cron_status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('verified', 'yes');
    }

    public function getIncomeAttribute()
    {
        $payments = Payment::withoutGlobalScopes()
            ->where('status', 'completed')->whereNotNull('paid_on')->where('company_id', $this->id);

        return ($payments->sum('amount') - $payments->sum('commission'));
    }

    public function getExpensesAttribute()
    {
        $payments = Tout::withoutGlobalScopes()
            ->where('status', 'completed')->whereNotNull('paid_on')->where('company_id', $this->id);

        return $payments->sum('amount');
    }

    public function googleAccount()
    {
        return $this->hasOne(GoogleAccount::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
