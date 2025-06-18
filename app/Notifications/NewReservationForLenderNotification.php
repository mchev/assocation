<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReservationForLenderNotification extends Notification implements ShouldQueue
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
            ->subject($this->reservation->lenderOrganization->name.' - Nouvelle réservation')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line("Une nouvelle demande de réservation a été créée par {$this->reservation->user->name} de {$this->reservation->borrowerOrganization->name}.")
            ->line("Équipements demandés : {$this->reservation->items->map(fn ($item) => "{$item->equipment->name} (x{$item->quantity})")->join(', ')}")
            ->line("Période : du {$this->reservation->start_date->format('d/m/Y')} au {$this->reservation->end_date->format('d/m/Y')}")
            ->action('Gérer la réservation', route('app.organizations.reservations.out.edit', $this->reservation))
            ->line("Merci d'examiner cette demande dès que possible. Dans {$this->reservation->deadlineForHuman} à partir de la réception de cet email, la réservation sera automatiquement annulée.");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'message' => "Nouvelle demande de réservation de {$this->reservation->borrowerOrganization->name}",
            'action_url' => route('app.organizations.reservations.out.edit', $this->reservation),
        ];
    }
}
