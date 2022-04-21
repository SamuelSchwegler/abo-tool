<?php

namespace App\Notifications;

use App\Models\Buy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
            ->line('Besten dank für Ihre Bezahlung. Sie erhalten nun ihre Gemüsepakete geliefert.')
            ->line('Sollten Sie einen Liefertermin nicht wahrnehmen können (bsp durch Ferienabwesenheiten), können Sie die Liefertermine online verwalten.')
            ->action('Lieferungen verwalten', url(env('APP_URL').'/my-orders'))
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
