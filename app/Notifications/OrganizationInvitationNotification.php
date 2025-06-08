<?php

namespace App\Notifications;

use App\Models\OrganizationInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrganizationInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected OrganizationInvitation $invitation
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invitation à rejoindre '.$this->invitation->organization->name)
            ->line('Vous avez été invité à rejoindre '.$this->invitation->organization->name.'.')
            ->line('Si vous n\'avez pas encore de compte, vous pourrez en créer un lors de l\'acceptation de l\'invitation.')
            ->action('Accepter l\'invitation', url('/invitations/'.$this->invitation->token))
            ->line('Si vous n\'attendiez pas cette invitation, vous pouvez ignorer cet email.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'organization_id' => $this->invitation->organization_id,
            'role_id' => $this->invitation->role_id,
        ];
    }
}
