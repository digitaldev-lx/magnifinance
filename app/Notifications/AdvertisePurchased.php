<?php

namespace App\Notifications;

use App\Advertise;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdvertisePurchased extends BaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $advertise;

    public function __construct(Advertise $advertise)
    {
        parent::__construct();
        $this->advertise = $advertise;
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
            ->view('emails.new_advertise_alert_to_superadmin', ['advertise' => $this->advertise, 'socialLinks' => $this->socialLinks, 'globalSetting' => $this->globalSetting]);
    }
}
