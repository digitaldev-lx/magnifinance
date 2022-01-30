<?php
/**
 * Created by PhpStorm.
 * User: DEXTER
 * Date: 24/05/17
 * Time: 11:29 PM
 */

namespace App\Traits;

use App\Company;
use App\GlobalSetting;
use App\SmtpSetting;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Support\Facades\Config;

trait SmtpSettings
{

    public function setMailConfigs()
    {
        $smtpSetting = SmtpSetting::first();

        if ($smtpSetting)
        {
            $settings = GlobalSetting::select('company_name', 'company_email', 'logo', 'website')->first();

            $companyName = $settings ? $settings->company_name : $smtpSetting->mail_from_name;
            $companyEmail = $settings ? $settings->company_email : $smtpSetting->mail_from_email;

            if (!in_array(\config('app.env'), ['development','demo']))
            {
                Config::set('mail.driver', $smtpSetting->mail_driver);
                Config::set('mail.host', $smtpSetting->mail_host);
                Config::set('mail.port', $smtpSetting->mail_port);
                Config::set('mail.username', $smtpSetting->mail_username);
                Config::set('mail.password', $smtpSetting->mail_password);
                Config::set('mail.encryption', $smtpSetting->mail_encryption);
            }

            Config::set('mail.reply_to.name', $companyName);
            Config::set('mail.reply_to.address', $companyEmail);

            // SES and other mail services which require email from verified sources
            if (\config('mail.verified') === true) {
                Config::set('mail.from.address', $smtpSetting->mail_from_email);
            }
            else {
                Config::set('mail.from.address', $companyEmail);
            }

            Config::set('mail.from.name', $companyName);

            Config::set('app.name', $settings->company_name);
            Config::set('app.logo', $settings->logo_url);

            (new MailServiceProvider(app()))->register();
            $_ENV['APP_URL'] = $settings->website;
        }

    }

}
