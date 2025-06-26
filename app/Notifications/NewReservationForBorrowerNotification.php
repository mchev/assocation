<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NewReservationForBorrowerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Reservation $reservation
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->reservation->borrowerOrganization->name.' - Nouvelle réservation')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line("Votre demande de réservation pour {$this->reservation->borrowerOrganization->name} a bien été enregistrée.")
            ->line('--------------------------------')
            ->line('Message de la demande :')
            ->line(new HtmlString(
                '<pre style="white-space:pre-wrap;font-family:monospace">'.
                    e($this->reservation->notes).
                '</pre>'
            ))
            ->line('--------------------------------')
            ->line('Vous recevrez un email lorsque votre demande sera acceptée ou refusée.')
            ->action('Gérer la réservation', route('app.organizations.reservations.in.edit', $this->reservation))
            ->line("L'organisation qui possède l'équipement a {$this->reservation->deadlineForHuman} pour répondre à votre demande. Au delà de ce délai, votre demande sera automatiquement annulée.");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'message' => "Nouvelle demande de réservation de {$this->reservation->borrowerOrganization->name}",
            'action_url' => route('app.organizations.reservations.in.edit', $this->reservation),
        ];
    }
}
