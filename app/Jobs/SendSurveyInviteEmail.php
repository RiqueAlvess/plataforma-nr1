<?php

namespace App\Jobs;

use App\Mail\SurveyInviteMail;
use App\Models\Campaign;
use App\Models\SurveyInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSurveyInviteEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 10;

    public function __construct(
        private readonly SurveyInvite $invite,
        private readonly string $emailDestino,
        private readonly Campaign $campaign
    ) {}

    public function handle(): void
    {
        $surveyUrl = url("/pesquisa/{$this->invite->token}");

        Mail::to($this->emailDestino)
            ->send(new SurveyInviteMail($this->invite, $this->campaign, $surveyUrl));

        $this->invite->update([
            'envio_status' => 'enviado',
            'enviado_em'   => now(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->invite->update(['envio_status' => 'erro']);
    }
}
