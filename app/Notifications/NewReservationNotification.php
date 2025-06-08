<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReservationNotification extends Notification implements ShouldQueue
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
        $equipments = $this->reservation->items->map(fn ($item) => "{$item->equipment->name} (x{$item->quantity})"
        )->join(', ');

        return (new MailMessage)
            ->subject('Nouvelle demande de réservation')
            ->greeting('Bonjour,')
            ->line("Une nouvelle demande de réservation a été créée par {$this->reservation->borrowerOrganization->name}.")
            ->line("Équipements demandés : {$equipments}")
            ->line("Période : du {$this->reservation->start_date->format('d/m/Y')} au {$this->reservation->end_date->format('d/m/Y')}")
            ->action('Voir la réservation', route('organizations.reservations.show', [
                'organization' => $this->reservation->lenderOrganization,
                'reservation' => $this->reservation,
            ]))
            ->line("Merci d'examiner cette demande dès que possible.");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'message' => "Nouvelle demande de réservation de {$this->reservation->borrowerOrganization->name}",
            'action_url' => route('organizations.reservations.show', [
                'organization' => $this->reservation->lenderOrganization,
                'reservation' => $this->reservation,
            ]),
        ];
    }
}
