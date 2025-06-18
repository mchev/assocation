<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RejectedReservationNotification extends Notification implements ShouldQueue
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
            ->subject('Mise à jour de votre réservation')
            ->greeting('Bonjour!')
            ->line('Nous avons une mise à jour concernant votre réservation auprès de '.$this->reservation->lenderOrganization->name.'.')
            ->line('Malheureusement, cette réservation n\'a pas pu être confirmée, mais ne vous inquiétez pas - de nombreuses autres options s\'offrent à vous!')
            ->line('Découvrez notre large sélection d\'équipements disponibles auprès d\'autres organisations partenaires.')
            ->action('Explorer les équipements', route('app.organizations.index'))
            ->line('Vous pouvez également consulter les détails de cette réservation pour plus d\'informations.')
            ->action('Voir ma réservation', route('app.organizations.reservations.in.edit', $this->reservation))
            ->line('Cordialement,')
            ->line('L\'équipe de '.config('app.name'));
    }
}
