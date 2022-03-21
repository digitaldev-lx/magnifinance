<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendPaymentLinkNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    private $stripe;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($stripe)
    {
        //
        $this->stripe = $stripe;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['mail'];

        /*if ($this->smsSetting->nexmo_status == 'active' && $notifiable->mobile_verified == 1) {
            array_push($via, 'nexmo');
        }*/

        return $via;
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
            ->from([config("mail.from.name"), config("mail.from.address")])
            ->greeting(__("email.hello") . " " . $notifiable->name)
            ->line(__('email.thankForYourContactAndInterest'))
            ->line(__('email.confirmAppointmentPaymentButton'))
            ->line(__('app.expires') . " " . __('app.at') . " - " . Carbon::createFromTimestamp($this->stripe->expires_at)->toDateTimeString())
            ->action(__("app.click") . " " . __("app.to") . " " . __("app.pay"), $this->stripe->url)
            ->salutation(__("email.excitedToHaveYou"));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
