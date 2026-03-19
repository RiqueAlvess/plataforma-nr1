<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    public function __construct(private readonly string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->email));

        return (new MailMessage())
            ->subject('Recuperação de Senha - ' . config('app.name'))
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Você solicitou a recuperação da sua senha.')
            ->line('Clique no botão abaixo para criar uma nova senha. Este link expira em 60 minutos.')
            ->action('Recuperar Senha', $url)
            ->line('Se você não solicitou a recuperação de senha, ignore este email.')
            ->salutation('Equipe ' . config('app.name'));
    }
}
