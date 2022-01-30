<?php

namespace App\Notifications;

use App\FooterSetting;
use App\GlobalSetting;
use App\SmsSetting;
use Illuminate\Notifications\Messages\MailMessage;

class SuperadminNotificationAboutNewAddedCompany extends BaseNotification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $company;

    public function __construct($company_data)
    {
        parent::__construct();
        $this->company = $company_data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    // @codingStandardsIgnoreLine
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    // @codingStandardsIgnoreLine
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('email.appointo', ['name' => $this->globalSetting->company_name]))
            ->view('emails.new_company_alert_to_superadmin', ['user' => $this->company, 'socialLinks' => $this->socialLinks, 'globalSetting' => $this->globalSetting]);
    }

}
