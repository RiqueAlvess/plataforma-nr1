<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountLockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Conta Bloqueada - ' . config('app.name'))
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Sua conta foi bloqueada após múltiplas tentativas de login malsucedidas.')
            ->line('Para recuperar o acesso, utilize o link de recuperação de senha abaixo.')
            ->action('Recuperar Senha', url('/forgot-password'))
            ->line('Se você não tentou fazer login, entre em contato com o suporte imediatamente.')
            ->salutation('Equipe ' . config('app.name'));
    }
}
