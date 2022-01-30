<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ContactUs extends BaseNotification
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
    // @codingStandardsIgnoreLine
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('email.newContact.subject'))
            ->line(__('email.contactUsEmail'))
            ->line(__('email.name').' '.request('name'))
            ->line(__('email.email').' '.request('email'))
            ->line(__('email.details').' '.request('details'))
            ->salutation(new HtmlString(__('email.regards').',<br>'.request('name')));
    }

}
