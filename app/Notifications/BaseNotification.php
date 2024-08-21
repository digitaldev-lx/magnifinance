<?php

namespace App\Notifications;

use App\Models\FooterSetting;
use App\Models\GlobalSetting;
use App\Models\SmsSetting;
use App\Traits\SmsSettings;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BaseNotification extends Notification
{

    use Queueable, SmtpSettings, SmsSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    protected $smsSetting;
    protected $socialLinks;
    protected $globalSetting;

    public function __construct()
    {
        $this->smsSetting = SmsSetting::first();
        $this->socialLinks = FooterSetting::first()->social_links;
        $this->globalSetting = GlobalSetting::first();

        $this->setMailConfigs();
        $this->setSmsConfigs();
    }

}
