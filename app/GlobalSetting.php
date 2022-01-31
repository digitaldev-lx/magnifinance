<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * App\GlobalSetting
 *
 * @property int $id
 * @property int $currency_id
 * @property string $company_name
 * @property string $company_email
 * @property string $company_phone
 * @property string $contact_email
 * @property string|null $logo
 * @property string $address
 * @property string $date_format
 * @property string $time_format
 * @property string $website
 * @property string $timezone
 * @property string $locale
 * @property string $sign_up_note
 * @property string $terms_note
 * @property string|null $purchase_code
 * @property string|null $supported_until
 * @property string $map_option
 * @property string|null $map_key
 * @property string $google_calendar
 * @property string|null $google_client_id
 * @property string|null $google_client_secret
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Currency $currency
 * @property-read mixed $formatted_address
 * @property-read mixed $formatted_phone_number
 * @property-read mixed $formatted_website
 * @property-read mixed $logo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereCompanyEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereCompanyPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereDateFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereGoogleCalendar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereGoogleClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereGoogleClientSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereMapKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereMapOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting wherePurchaseCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereSignUpNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereSupportedUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereTermsNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereTimeFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GlobalSetting whereWebsite($value)
 * @mixin \Eloquent
 */
class GlobalSetting extends Model
{
    use Notifiable;

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    protected $appends = [
        'logo_url',
        'formatted_phone_number',
        'formatted_address',
        'formatted_website'
    ];

    public function getLogoUrlAttribute()
    {
        if (is_null($this->logo)) {
            return asset('storage/images/logo/logo.png');
        }

        return asset($this->logo);
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

}
