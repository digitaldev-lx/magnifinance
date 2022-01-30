<?php

namespace App\Notifications;

use App\FooterSetting;
use App\GlobalSetting;
use App\SmsSetting;
use App\Traits\SmsSettings;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BaseNotification extends Notification implements ShouldQueue
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
