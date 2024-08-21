<?php

namespace App\Notifications;

use App\Models\Tout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ToutCompanyInfo extends BaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $tout;

    public function __construct(Tout $tout)
    {
        parent::__construct();
        $this->tout = $tout;
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
        return ['mail', 'database'];
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
            'booking_id' => $this->tout->id,
            'from' => $this->tout->from,
            'to' => $this->tout->to,
            'amount' => $this->tout->amount,
            'paid_on' => $this->tout->paid_on,
        ];
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
            ->view('emails.new_tout_alert_to_company', ['tout' => $this->tout, 'socialLinks' => $this->socialLinks, 'globalSetting' => $this->globalSetting]);
    }
}
