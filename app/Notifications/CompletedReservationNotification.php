<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompletedReservationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Réservation terminée')
            ->greeting('Bonjour!')
            ->line('La réservation auprès de '.$this->reservation->lenderOrganization->name.' est terminée.')
            ->line('Si vous recevez cette notification, c\'est que tous les équipements ont été rendus.')
            ->action('Consulter la réservation', route('app.organizations.reservations.in.edit', $this->reservation))
            ->line('Cordialement,')
            ->line('L\'équipe de '.config('app.name'));
    }
}
