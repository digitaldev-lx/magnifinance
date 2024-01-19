<?php

use App\Company;
use App\Currency;
use App\GlobalSetting;
use Illuminate\Support\Str;
use App\Scopes\CompanyScope;
use App\CurrencyFormatSetting;
use Illuminate\Support\Facades\Artisan;

if (!function_exists('cdn_storage_url')) {

    function cdn_storage_url($path)
    {
       return "https://". config("digitalocean.spaces.DIGITALOCEAN_SPACES_BUCKET") .".". config("digitalocean.spaces.DIGITALOCEAN_SPACES_REGION") .".cdn.digitaloceanspaces.com/". $path;
    }

}

if (!function_exists('abort_and_log')) {

    function abort_and_log($code, $message)
    {
        Log::error($code ." - ". $message);
        abort($code, $message);
    }

}

if (!function_exists('log')) {

    function log($message)
    {
        Log::error($message);
//        ActivityLogger::activity($message);
    }

}

if (!function_exists('company')) {

    function company()
    {
        if (auth()->user())
        {
            $company = Company::find(auth()->user()->company_id);
            return $company;
        }

        return false;
    }

}

if (!function_exists('asset_url')) {

    // @codingStandardsIgnoreLine
    function asset_url($path)
    {
        $path = 'user-uploads/' . $path;
        $storageUrl = $path;

        if (!Str::startsWith($storageUrl, 'http')) {
            return url($storageUrl);
        }

        return $storageUrl;
    }

}

if (!function_exists('isRunningInConsoleOrSeeding')) {

    /**
     * Check if app is seeding data
     * @return boolean
     */
    function isRunningInConsoleOrSeeding()
    {
        // We set config(['app.seeding' => true]) at the beginning of each seeder. And check here
        return app()->runningInConsole() || isSeedingData();
    }

}

if (!function_exists('isSeedingData')) {

    /**
     * Check if app is seeding data
     * @return boolean
     */
    function isSeedingData()
    {
        // We set config(['app.seeding' => true]) at the beginning of each seeder. And check here
        return config('app.seeding');
    }

}


if (!function_exists('checkMigrateStatus')) {

    function checkMigrateStatus()
    {

        if (!session()->has('checkMigrateStatus')) {

            $status = Artisan::call('migrate:check');

            if ($status && !request()->ajax()) {
                Artisan::call('migrate', array('--force' => true)); // Migrate database
                Artisan::call('optimize:clear');
            }

            session(['checkMigrateStatus' => 'Good']);
        }

        return session('checkMigrateStatus');
    }

}

if (!function_exists('currencyConvertedPrice')) {

    function currencyConvertedPrice($company_id, $price)
    {
        // Get exchange rates
        $from_currency = cache()->remember('from_currency_'.$company_id, 60*60, function () use ($company_id) {
            return Company::withoutGlobalScope(CompanyScope::class)->find($company_id)->currency->exchange_rate;
        });

        $to_currency = cache()->remember('globalSettings', 60*60, function () {
            return GlobalSetting::first()->currency->exchange_rate;
        });
        try {
            // Convert amount
            $value = ($price * $to_currency) / $from_currency;
        } catch (Exception $e) {
            // Prevent invalid conversion or division by zero errors
            $value = $price;
        }

        return round($value, 2);
    }

}

if (!function_exists('currencyConvertFromTo')) {

    function currencyConvertFromTo($from_currency_id, $to_currency_id, $price)
    {
        // Get exchange rates
        $from_currency = Currency::find($from_currency_id)->exchange_rate;
        $to_currency = Currency::find($to_currency_id)->exchange_rate;
        try {
            // Convert amount
            $value = ($price * $to_currency) / $from_currency;
        } catch (Exception $e) {
            // Prevent invalid conversion or division by zero errors
            $value = $price;
        }

        return round($value, 2);
    }

}

if (!function_exists('convertedOriginalPrice')) {

    function convertedOriginalPrice($company_id, $price)
    {
        // Get exchange rates
        $to_currency = Company::withoutGlobalScope(CompanyScope::class)->find($company_id)->currency->exchange_rate;
        $from_currency = GlobalSetting::first()->currency->exchange_rate;
        try {
            // Convert amount
            $value = ($price * $to_currency) / $from_currency;
        } catch (Exception $e) {
            // Prevent invalid conversion or division by zero errors
            $value = $price;
        }

        return round($value, 2);
    }

}

// Get currency symbol of user
if (!function_exists('myCurrencySymbol')) {

    function myCurrencySymbol()
    {
        if (!session()->has('myCurrencySymbol')) {
            $currency_symbol = company() ? company()->currency->currency_symbol : globalSetting()->currency->currency_symbol;
            session(['myCurrencySymbol' => $currency_symbol]);
        }

        return session('myCurrencySymbol');
    }

}

// Format currency
if (!function_exists('currencyFormatter')) {

    function currencyFormatter($amount, $currency_symbol = null)
    {

        $formats = currencyFormatSetting();

        $currency_symbol = $currency_symbol ? $currency_symbol : globalSetting()->currency->currency_symbol;

        $currency_position = $formats->currency_position;
        $no_of_decimal = !is_null($formats->no_of_decimal) ? $formats->no_of_decimal : '0';
        $thousand_separator = !is_null($formats->thousand_separator) ? $formats->thousand_separator : '';
        $decimal_separator = !is_null($formats->decimal_separator) ? $formats->decimal_separator : '0';
        $amount = number_format($amount, $no_of_decimal, $decimal_separator, $thousand_separator);

        switch ($currency_position)
        {
        case 'right':
            $amount = $currency_symbol . $amount;
                break;
        case 'left_with_space':
            $amount = $amount . ' ' . $currency_symbol;
                break;
        case 'right_with_space':
            $amount = $currency_symbol . ' ' . $amount;
                break;
        default:
            $amount = $amount . $currency_symbol;
                break;
        }

        return $amount;
    }

}

// Create cache format currency settings to reduce database load
if (!function_exists('currencyFormatSetting')) {

    function currencyFormatSetting()
    {
        if (!cache()->has('currencyFormatSetting')) {
            $setting = CurrencyFormatSetting::first();
            cache(['currencyFormatSetting' => $setting], 60 * 60 * 24); /** @phpstan-ignore-line */
        }

        return cache('currencyFormatSetting'); /** @phpstan-ignore-line */
    }

}

// Create cache global settings to reduce database load
if (!function_exists('globalSetting')) {

    function globalSetting()
    {
        return cache()->remember('GlobalSetting', 60*60, function () {
            return GlobalSetting::with('currency')->first();
        });
    }

}


// Create cache global settings to reduce database load
/*
if (!function_exists('extractDateTimeFormat')) {

    function extractDateTimeFormat($string) {
        switch ($string):
            case :

                'd-m-Y'
'm-d-Y'
'Y-m-d'
'd.m.Y'
'm.d.Y'
'Y.m.d'
'd/m/Y'
'm/d/Y'
'Y/m/d'
'd-M-Y'
'd/M/Y'
'd.M.Y'
'd M Y'
'd F, Y'
'D/M/Y'
'D.M.Y'
'D-M-Y'
'D M Y'
'd D M Y'
'D d M Y'
'dS M Y'
        $string = preg_replace('/\b\d{4}-\d{2}-\d{2}\b/', 'Y-m-d');
        $string = preg_replace('\d{2}\b/-\d{2}-/\b\d{4}', 'd-m-Y');
        $string = preg_replace('/\b\d{2}\/\d{2}\/\d{4}\b/', 'm/d/Y');
        $string = preg_replace('/\b\d{2}\.\d{2}\.\d{4}\b/', 'd.m.Y');
        $string = preg_replace('/\b\d{2}:\d{2}\b:\d{2}\b/', 'H:i:s');
        $string = preg_replace('/\b\d{2}:\d{2}\b/', 'H:i');
        $string = preg_replace('/\.\d{3}\b/', '.v');

        if (preg_match('/\d/', $string)
        {
            return false;
        }

        return $string;
    }
}*/

// Convert into Minutes of given Duration and Duration Type
if (!function_exists('convertToMinutes')) {

    function convertToMinutes($duration,$duration_type)
    {
        $durationTypeVal = 1; // Minutes value

        switch ($duration_type) {
        case 'minutes':
            $durationTypeVal = 1;
            break;
        case 'hours':
            $durationTypeVal = 60;
            break;
        case 'days':
            $durationTypeVal = 24 * 60;
            break;
        case 'weeks':
            $durationTypeVal = 7 * 24 * 60;
            break;
        default:
            $durationTypeVal = 1;
            break;
        }

        return ($duration * $durationTypeVal);
    }

}

if (!function_exists('abort_403')) {

    /**
     * Check if app is running unit tests
     * @return boolean
     */
    // @codingStandardsIgnoreLine
    function abort_403($condition)
    {
        abort_if($condition, 403, __('messages.permissionDenied')); /** @phpstan-ignore-line */
    }

}
