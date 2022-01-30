<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class CompanyWelcome extends BaseNotification
{

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
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('email.welcomeEmail') .' '. $this->globalSetting->company_name)
            ->view('emails.company_welcome_email', [
                'user' => $notifiable,
                'socialLinks' => $this->socialLinks,
                'globalSetting' => $this->globalSetting
            ]);
    }

}
