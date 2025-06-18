<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmedReservationNotification extends Notification implements ShouldQueue
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
            ->subject('Réservation confirmée')
            ->greeting('Bonjour!')
            ->line('Votre réservation auprès de '.$this->reservation->lenderOrganization->name.' a été acceptée.')
            ->line('Vous pouvez désormais accéder à votre réservation en cliquant sur le bouton ci-dessous.')
            ->action('Voir ma réservation', route('app.organizations.reservations.in.edit', $this->reservation))
            ->line('Cordialement,')
            ->line('L\'équipe de '.config('app.name'));
    }
}
