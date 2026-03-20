<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendAccountLockedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $userEmail,
        private readonly string $userName
    ) {}

    public function handle(): void
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.resend.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.resend.com/emails', [
            'from' => config('mail.from.address'),
            'to' => [$this->userEmail],
            'subject' => 'Sua conta foi bloqueada',
            'html' => "<p>Olá, {$this->userName}.</p>
                <p>Sua conta foi bloqueada devido a múltiplas tentativas de login malsucedidas.</p>
                <p>Entre em contato com o administrador para desbloquear.</p>",
        ]);

        if (!$response->successful()) {
            Log::error("Failed to send lock email to {$this->userEmail}: " . $response->body());
            throw new \RuntimeException("Email send failed");
        }
    }
}
