<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
        public string $previousStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = match ($this->reservation->status->value) {
            'confirmed' => "Votre demande de réservation a été acceptée par {$this->reservation->lenderOrganization->name}.",
            'rejected' => "Votre demande de réservation a été refusée par {$this->reservation->lenderOrganization->name}.",
            'cancelled' => "La réservation a été annulée. Raison : {$this->reservation->cancellation_reason}",
            default => "Le statut de votre réservation a été mis à jour ({$this->reservation->status->label()})."
        };

        $equipments = $this->reservation->items->map(fn ($item) => "{$item->equipment->name} (x{$item->quantity})"
        )->join(', ');

        return (new MailMessage)
            ->subject('Mise à jour de votre réservation')
            ->greeting('Bonjour,')
            ->line($message)
            ->line("Équipements concernés : {$equipments}")
            ->line("Période : du {$this->reservation->start_date->format('d/m/Y')} au {$this->reservation->end_date->format('d/m/Y')}")
            ->action('Voir la réservation', route('organizations.reservations.show', [
                'organization' => $this->reservation->borrowerOrganization,
                'reservation' => $this->reservation,
            ]));
    }

    public function toArray(object $notifiable): array
    {
        $statusLabel = $this->reservation->status->label();

        return [
            'reservation_id' => $this->reservation->id,
            'message' => "Statut de la réservation mis à jour : {$statusLabel}",
            'previous_status' => $this->previousStatus,
            'new_status' => $this->reservation->status->value,
            'action_url' => route('organizations.reservations.show', [
                'organization' => $this->reservation->borrowerOrganization,
                'reservation' => $this->reservation,
            ]),
        ];
    }
}
