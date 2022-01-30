<?php

namespace App\Notifications;

use PDF;
use App\Booking;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;

class NewBooking extends BaseNotification
{
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
        $via = ['mail'];

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

        $booking = $this->booking;
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.booking.receipt', compact('booking') );
        $filename = __('app.receipt').' #'.$this->booking->id;

        $mail = new MailMessage();

        $mail->subject(__('email.newBooking.subject').' '.config('app.name').'!')
            ->greeting(__('email.hello').' '.ucwords($notifiable->name).'!')
            ->line(__('email.newBooking.text'))
            ->line(__('app.booking').' #'.$this->booking->id);

        if(is_null($this->booking->deal_id)){
            $mail->line(__('app.booking').' '.__('app.date').' - '.$this->booking->date_time->format($this->booking->company->date_format.' '.$this->booking->company->time_format));
        }

            $mail->action(__('email.loginAccount'), url('/login'))
                ->line(__('email.thankyouNote'));

        if(!is_null($this->booking->deal_id)){
            $mail->attachData($pdf->output(), $filename);
        }

            return $mail->salutation(new HtmlString(__('email.regards').',<br>'.config('app.name')));

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

        if(is_null($this->booking->deal_id)){
            return (new NexmoMessage)
                ->content(
                __('email.newBooking.text')."\n".
                __('app.booking').' #'.$this->booking->id."\n".
                __('app.booking').' '.__('app.date').' - '.$this->booking->date_time->format($this->booking->company->date_format.' '.$this->booking->company->time_format))->unicode();
        }
        else
        {
            return (new NexmoMessage)
                ->content(
                __('email.newBooking.text')."\n".
                __('app.booking').' #'.$this->booking->id."\n")
                ->unicode();
        }
    }

    // @codingStandardsIgnoreLine
    public function toMsg91($notifiable)
    {

        if(is_null($this->booking->deal_id)){
            return (new \Craftsys\Notifications\Messages\Msg91SMS)
                ->from($this->smsSetting->msg91_from)
                ->content(
                __('email.newBooking.text')."\n".
                __('app.booking').' #'.$this->booking->id."\n".
                __('app.booking').' '.__('app.date').' - '.$this->booking->date_time->format($this->booking->company->date_format.' '.$this->booking->company->time_format));
        }
        else
        {
            return (new \Craftsys\Notifications\Messages\Msg91SMS)
                ->from($this->smsSetting->msg91_from)
                ->content(
                __('email.newBooking.text')."\n".
                __('app.booking').' #'.$this->booking->id."\n");
        }

    }

}
