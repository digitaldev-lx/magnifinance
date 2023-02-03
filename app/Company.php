<?php

namespace App;

use Carbon\Carbon;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;
use App\Observers\CompanyObserver;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Company
 *
 * @property int $id
 * @property int|null $currency_id
 * @property int|null $package_id
 * @property string $company_name
 * @property string $vat_number
 * @property boolean $magnifinance_active
 * @property string $partner_token
 * @property string $post_code
 * @property string $city
 * @property int|null $country_id
 * @property string|null $slug
 * @property string $company_email
 * @property string $company_phone
 * @property string|null $logo
 * @property string $address
 * @property string $date_format
 * @property string $time_format
 * @property string|null $website
 * @property string $timezone
 * @property string $locale
 * @property string|null $trial_ends_at
 * @property string|null $licence_expire_on
 * @property string $disable_slot
 * @property string $booking_time_type
 * @property string|null $booking_per_day
 * @property string $employee_selection
 * @property string|null $multi_task_user
 * @property string|null $popular_store
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $status
 * @property string $verified
 * @property string|null $stripe_id
 * @property string|null $card_brand
 * @property string|null $card_last_four
 * @property string|null $package_type
 * @property string $cron_status
 * @property int $duration
 * @property string $duration_type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Booking[] $bookingNotNotify
 * @property-read int|null $booking_not_notify_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookingNotification[] $bookingNotification
 * @property-read int|null $booking_notification_count
 * @property-read \App\Currency|null $currency
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Deal[] $deals
 * @property-read int|null $deals_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\GatewayAccountDetail[] $gatewayAccountDetails
 * @property-read int|null $gateway_account_details_count
 * @property-read mixed $company_verification_url
 * @property-read mixed $formatted_address
 * @property-read mixed $formatted_phone_number
 * @property-read mixed $formatted_website
 * @property-read mixed $income
 * @property-read mixed $expenses
 * @property-read mixed $logo_url
 * @property-read \App\GoogleAccount|null $googleAccount
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ModuleSetting[] $moduleSetting
 * @property-read int|null $module_setting_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\User|null $owner
 * @property-read \App\Package|null $package
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Spotlight[] $spotlight
 * @property-read int|null $spotlight_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Cashier\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $user
 * @property-read int|null $user_count
 * @property-read \App\VendorPage|null $vendorPage
 * @method static Builder|Company active()
 * @method static Builder|Company cronActive()
 * @method static Builder|Company newModelQuery()
 * @method static Builder|Company newQuery()
 * @method static Builder|Company query()
 * @method static Builder|Company verified()
 * @method static Builder|Company whereAddress($value)
 * @method static Builder|Company whereBookingPerDay($value)
 * @method static Builder|Company whereBookingTimeType($value)
 * @method static Builder|Company whereCardBrand($value)
 * @method static Builder|Company whereCardLastFour($value)
 * @method static Builder|Company whereCompanyEmail($value)
 * @method static Builder|Company whereCompanyName($value)
 * @method static Builder|Company whereCompanyPhone($value)
 * @method static Builder|Company whereCreatedAt($value)
 * @method static Builder|Company whereCronStatus($value)
 * @method static Builder|Company whereCurrencyId($value)
 * @method static Builder|Company whereDateFormat($value)
 * @method static Builder|Company whereDisableSlot($value)
 * @method static Builder|Company whereDuration($value)
 * @method static Builder|Company whereDurationType($value)
 * @method static Builder|Company whereEmployeeSelection($value)
 * @method static Builder|Company whereId($value)
 * @method static Builder|Company whereLicenceExpireOn($value)
 * @method static Builder|Company whereLocale($value)
 * @method static Builder|Company whereLogo($value)
 * @method static Builder|Company whereMultiTaskUser($value)
 * @method static Builder|Company wherePackageId($value)
 * @method static Builder|Company wherePackageType($value)
 * @method static Builder|Company wherePopularStore($value)
 * @method static Builder|Company whereSlug($value)
 * @method static Builder|Company whereStatus($value)
 * @method static Builder|Company whereStripeId($value)
 * @method static Builder|Company whereTimeFormat($value)
 * @method static Builder|Company whereTimezone($value)
 * @method static Builder|Company whereTrialEndsAt($value)
 * @method static Builder|Company whereUpdatedAt($value)
 * @method static Builder|Company whereVerified($value)
 * @method static Builder|Company whereWebsite($value)
 * @mixin \Eloquent
 */

class Company extends Model
{
    use Notifiable, Billable, SpatialTrait;

    protected $spatialFields = [
        'lat_long'
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
        $globalSetting = GlobalSetting::first();

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
