<?php

namespace App\Notifications;

use App\Leave;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class newLeave extends BaseNotification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $leave;

    public function __construct(Leave $leave)
    {
        parent::__construct();
        $this->leave = $leave;
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
    public function toMail($notifiable)
    {
        $start_date = $this->leave->start_date;

        $newStartDate = '';
        $newEndDate = '';

        if ($start_date) {
            $newStartDate = \Carbon\Carbon::createFromFormat('Y-m-d', $start_date)
            ->format('d-m-Y');
        }

        $end_date = $this->leave->end_date;

        if ($end_date) {
            $newEndDate = \Carbon\Carbon::createFromFormat('Y-m-d', $end_date)
            ->format('d-m-Y');
        }

        $mailMessage = new MailMessage();

        $mailMessage->subject(__('email.newLeave.subject').'')
            ->greeting(__('email.hello').' '.ucwords($notifiable->name).' !')
            ->line(__('email.newLeave.leaveDetails').' '.ucwords($this->leave->employee->name).'')
            ->line(__('email.newLeave.fromDate').' '.$newStartDate);

        if(!is_null($this->leave->end_date) && $this->leave->start_date != $this->leave->end_date) {
            $mailMessage->line(__('email.newLeave.toDate').' '.$newEndDate);
        }

        $mailMessage->line(__('email.newLeave.leave_type').' '.$this->leave->leave_type);

        if($this->leave->leave_type == 'Half day' && $this->leave->start_time != null) {
            $mailMessage->line(__('email.newLeave.fromTime').' '.\Carbon\Carbon::parse($this->leave->start_time)->translatedFormat('h:i A'));
            $mailMessage->line(__('email.newLeave.toTime').' '.\Carbon\Carbon::parse($this->leave->end_time)->translatedFormat('h:i A'));
        }

        $mailMessage->line(__('email.newLeave.reason').' '.$this->leave->reason);
        $mailMessage->action(__('email.loginAccount'), url('/login'));

        $mailMessage->salutation(new HtmlString(__('email.thankyouNote').',<br>'.ucwords($this->leave->employee->name).''));

        return $mailMessage;
    }

}
