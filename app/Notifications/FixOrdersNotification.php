<?php

namespace App\Notifications;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FixOrdersNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public Customer $customer)
    {
        //
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
            ->subject('Korrigierte Lieferungen im Gemüseabotool')
            ->greeting('Hey '.$notifiable->email)
            ->line('Hier ist das Gemüseabotool. Ich habe soeben automatisiert fehlende Lieferungen für '.$this->customer->name.' korrigiert.')
            ->action('Lieferübersicht', url('/customer/'.$this->customer->id.'/orders'))
            ->line('Bitte prüfe, ob alles seine Richtigkeit hat. Dieses Email geht an alle Admins.');
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
