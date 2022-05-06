<?php

namespace App\Notifications;

use App\Models\Buy;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ConfirmPayment extends Notification
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
            ->subject('Bestätigung Zahlungseingang')
            ->greeting('Guten Tag '.$notifiable->customer->name)
            ->line('Ihre Zahlung wurde registriert und wir freuen uns, in den kommenden Tagen die erste Bio-Gemüse-
Lieferung für Sie vorzubereiten. Die genauen Daten finden Sie in Ihrem persönlichen Konto unter
‘Meine Lieferungen’. Hier können Sie auch allfällige Abwesenheiten verwalten, z.B. Ferien.')
            ->action('Lieferungen verwalten', url(env('APP_URL').'/my-orders'))
            ->line('Wir wünschen guten Appetit!')
            ->salutation(new HtmlString('Freundliche Grüsse<br>Gartenbauschule Hünibach'));
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
