<?php

namespace App\Notifications;

use App\OfflinePlanChange;
use Illuminate\Notifications\Messages\MailMessage;

class OfflinePackageChangeReject extends BaseNotification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $planChange;

    public function __construct(OfflinePlanChange $planChange)
    {
        parent::__construct();
        $this->planChange = $planChange;
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
            ->subject(__('email.offlinePackageChangeReject.subject'))
            ->greeting(__('email.hello') . '!')
            ->line(__('email.offlinePackageChangeReject.text', ['plan' => $this->planChange->package->name . ' (' . $this->planChange->package_type . ')']))
            ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    // @codingStandardsIgnoreLine
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

}
