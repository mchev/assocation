<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCancelledAutomaticallyNotification extends Notification implements ShouldQueue
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
            ->subject('Réservation annulée automatiquement')
            ->line('Votre réservation a été annulée automatiquement car l\'organisation n\'a pas répondu dans le délai.')
            ->action('Voir la réservation', route('app.organizations.reservations.in.edit', $this->reservation));
    }
}
