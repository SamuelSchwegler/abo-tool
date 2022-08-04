<?php

namespace App\Notifications;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderReminder extends Notification
{
    use Queueable;

    protected Order $order;
    protected Delivery $delivery;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->delivery = $order->delivery;

        $this->order->reminded = true;
        $this->order->saveQuietly();
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
            ->subject('Lieferung vom '.$this->delivery->date->format('d.m.Y'))
            ->greeting('Guten Morgen '.$notifiable->name)
            ->line('Die Abmeldefrist für Ihre nächste Bestellung läuft am '.$this->delivery->deadline->format('d.m.Y').' ab. Falls Sie abwesend sind, können Sie sich online abmelden.')
            ->action('Zur Lieferverwaltung', url('/my-orders'))
            ->line('Falls Sie sich nicht abmelden erhalten Sie die Lieferung am '.$this->delivery->date->format('d.m.Y').'.')
            ->line('Danke für Ihre Bestellungen');
    }
}
