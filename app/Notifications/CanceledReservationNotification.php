<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CanceledReservationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reservation $reservation, public string $type) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $borrowerMessage = (new MailMessage)
            ->subject('Réservation annulée')
            ->greeting('Bonjour!')
            ->line('La réservation auprès de '.$this->reservation->lenderOrganization->name.' a été annulée.')
            ->line('Vous pouvez consulter la réservation en cliquant sur le bouton ci-dessous.')
            ->action('Consulter la réservation', route('app.organizations.reservations.in.edit', $this->reservation))
            ->line('Cordialement,')
            ->line('L\'équipe de '.config('app.name'));

        $lenderMessage = (new MailMessage)
            ->subject('Réservation annulée')
            ->greeting('Bonjour!')
            ->line('La réservation effectuée par '.$this->reservation->borrowerOrganization->name.' a été annulée.')
            ->line('Vous pouvez consulter la réservation en cliquant sur le bouton ci-dessous.')
            ->action('Consulter la réservation', route('app.organizations.reservations.in.edit', $this->reservation))
            ->line('Cordialement,')
            ->line('L\'équipe de '.config('app.name'));

        return $this->type === 'borrower' ? $borrowerMessage : $lenderMessage;
    }
}
