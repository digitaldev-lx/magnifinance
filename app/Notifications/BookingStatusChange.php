<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Support\HtmlString;

class BookingStatusChange extends BaseNotification implements ShouldQueue
{

    use Queueable;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $booking;

    public function __construct(Booking $booking)
    {
        parent::__construct();

        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['mail', 'database'];

        if ($this->smsSetting->nexmo_status == 'active' && $notifiable->mobile_verified == 1) {
            array_push($via, 'nexmo');
        }

        if ($this->smsSetting->msg91_status == 'active' && $notifiable->mobile_verified == 1) {
            array_push($via, 'msg91');
        }

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
            ->subject(__('email.bookingStatusUpdated.subject').' '.config('app.name').'!')
            ->greeting(__('email.hello').' '.ucwords($notifiable->name).'!')
            ->line(__('email.bookingStatusUpdated.text') . ' : ' . __('app.'.$this->booking->status))
            ->line(__('email.bookingStatusUpdated.bookingDetails') . ':')
            ->line(__('app.booking').' #'.$this->booking->id)
            ->line(__('app.booking').' '.__('app.date').' - '.$this->booking->date_time->format($this->booking->company->date_format.' '.$this->booking->company->time_format))
            ->action(__('email.loginAccount'), url('/login'))
            ->line(__('email.thankyouNote'))
            ->salutation(new HtmlString(__('email.regards').',<br>'.config('app.name')));
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
        $items = $this->booking->items->map(function ($item){
            return $item->businessService->name;
        });

        return [
            'booking_id' => $this->booking->id,
            'date_time' => $this->booking->date_time,
            'services' => $items,
            'amount_to_pay' => $this->booking->amount_to_pay,
            'status' => $this->booking->status,
        ];
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
    // @codingStandardsIgnoreLine
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
            ->content(
                        __('email.bookingStatusUpdated.text') . ' : ' . __('app.'.$this->booking->status)."\n".
                        __('app.booking').' #'.$this->booking->id."\n".
                        __('app.booking').' '.__('app.date').' - '.$this->booking->date_time->format($this->booking->company->date_format.' '.$this->booking->company->time_format)
                    )->unicode();
    }

    // @codingStandardsIgnoreLine
    public function toMsg91($notifiable)
    {

            return (new \Craftsys\Notifications\Messages\Msg91SMS)
                ->from($this->smsSetting->msg91_from)
                ->content(
                    __('email.bookingStatusUpdated.text') . ' : ' . __('app.'.$this->booking->status)."\n".
                    __('app.booking').' #'.$this->booking->id."\n".
                    __('app.booking').' '.__('app.date').' - '.$this->booking->date_time->format($this->booking->company->date_format.' '.$this->booking->company->time_format)
                );
    }

}
