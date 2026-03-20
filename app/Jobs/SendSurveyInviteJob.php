<?php

namespace App\Jobs;

use App\Models\SurveyInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSurveyInviteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private readonly SurveyInvite $invite
    ) {}

    public function handle(): void
    {
        // Recuperar o email real é impossível (hash irreversível)
        // O email real NÃO está armazenado por conformidade com LGPD
        // Este job simula o envio ou usa um mecanismo alternativo

        // NOTA: Como o email é armazenado como hash irreversível (SHA-256),
        // não é possível enviar o email real a partir do hash.
        // Na prática, o email real deve ser passado de forma separada e segura
        // apenas durante o processo de envio, nunca armazenado em texto plano.

        // Para fins de demo, apenas logamos o envio
        Log::info("Survey invite dispatch: token={$this->invite->token} campaign={$this->invite->campaign_id}");

        // Quando o email real estiver disponível (passado temporariamente), enviar via Resend:
        // Http::withHeaders(['Authorization' => 'Bearer ' . config('services.resend.key')])
        //     ->post('https://api.resend.com/emails', [
        //         'from' => config('mail.from.address'),
        //         'to' => $emailReal,
        //         'subject' => 'Convite para pesquisa organizacional',
        //         'html' => view('emails.survey-invite', ['token' => $this->invite->token])->render(),
        //     ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->invite->update(['envio_status' => 'erro']);
        Log::error("Failed to send invite {$this->invite->id}: {$exception->getMessage()}");
    }
}
