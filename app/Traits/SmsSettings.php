<?php
/**
 * Created by PhpStorm.
 * User: DEXTER
 * Date: 24/05/17
 * Time: 11:29 PM
 */

namespace App\Traits;

use App\Models\GlobalSetting;
use App\Models\SmsSetting;
use Illuminate\Notifications\NexmoChannelServiceProvider;
use Illuminate\Support\Facades\Config;

trait SmsSettings
{

    public function setSmsConfigs()
    {
        $smsSetting = SmsSetting::first();
        $settings = GlobalSetting::first();

        $company = explode(' ', trim($settings->company_name));

        Config::set('services.nexmo.key', $smsSetting->nexmo_key);
        Config::set('services.nexmo.secret', $smsSetting->nexmo_secret);
        Config::set('services.nexmo.sms_from', $smsSetting->nexmo_from);

        Config::set('services.msg91.key', $smsSetting->msg91_key);
        Config::set('services.msg91.from', $smsSetting->msg91_from);

        Config::set('nexmo.api_key', $smsSetting->nexmo_key);
        Config::set('nexmo.api_secret', $smsSetting->nexmo_secret);

        Config::set('app.name', $settings->company_name);
        Config::set('app.logo', $settings->logo_url);

        (new NexmoChannelServiceProvider(app()))->register();
    }

}
