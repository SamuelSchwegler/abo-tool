<?php

namespace App\Notifications;

use App\Models\Buy;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SendInvoice extends Notification
{
    use Queueable;

    protected Buy $buy;
    protected bool $extension = false;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Buy $buy, bool $extension = false)
    {
        $this->buy = $buy;
        $this->extension = $extension;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail_message = (new MailMessage)
            ->subject('Ihre Rechnung vom Gemüse-Abo')
            ->greeting('Guten Tag ' . $notifiable->customer->name);

        if ($this->extension) {
            $mail_message = $mail_message
                ->line('Wir stellen Ihnen eine Rechnung für die Verlängerung ihres Gemüse-Abos zu. Herzlichen Dank für Ihre Treue!');
        } else {
            $mail_message = $mail_message
                ->line('Herzlichen Dank für die Bestellung des Gemüse-Abos! Sie erhalten hier die dazugehörende
Rechnung.');
        }

        return $mail_message->
        action('Rechnung herunterladen', url(route('buy.export.bill', $this->buy)))
            ->line('Sobald die Rechnung bezahlt und die Zahlungsbestätigung erfolgt ist, wird Ihr Gemüse-Abo
freigeschaltet. Vor der ersten Bio-Gemüse-Lieferung erhalten Sie von uns eine Benachrichtigung.')
            ->line('Danke, dass Sie sich für ein Gemüse-Abo der Gartenbauschule Hünibach entschieden haben. Damit
unterstützen Sie auch die Ausbildung von Bio-Gärtner:innen an unserer Schule!')
            ->salutation(new HtmlString('Freundliche Grüsse<br>Gartenbauschule Hünibach'));


    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
