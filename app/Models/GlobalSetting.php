<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

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
        return cdn_storage_url($this->logo);
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
