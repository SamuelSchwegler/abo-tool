<?php

namespace App\Notifications;

use App\Models\Buy;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendInvoice extends Notification
{
    use Queueable;

    protected Buy $buy;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Buy $buy)
    {
        $this->buy = $buy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
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
            ->subject('Ihre Rechnung')
            ->greeting('Guten Tag '.$notifiable->customer->name)
            ->line('Danke für Ihre Bestellung bei der Gartenbauschule Hünibach. Hier finden Sie die Rechnung.')
            ->action('Rechnung herunterladen', url(route('buy.export.bill', $this->buy)))
            ->line('Sobald Sie die Rechnung bezahlt haben, werden wir das Abo für Sie freischalten.')
            ->salutation('Wir wünschen Ihnen einen schönen Tag');
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
