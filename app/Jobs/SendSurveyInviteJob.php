<?php

namespace App\Jobs;

use App\Mail\SurveyInviteMail;
use App\Models\CsvRecord;
use App\Models\SurveyInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSurveyInviteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        private readonly SurveyInvite $invite
    ) {}

    public function handle(): void
    {
        $csvRecord = CsvRecord::where('email_hash', $this->invite->email_hash)
            ->whereNotNull('email')
            ->first();

        if (! $csvRecord) {
            return;
        }

        $campaign = $this->invite->campaign;
        $surveyUrl = url("/pesquisa/{$this->invite->token}");

        Mail::to($csvRecord->email)
            ->send(new SurveyInviteMail($this->invite, $campaign, $surveyUrl));
    }

    public function failed(\Throwable $exception): void
    {
        $this->invite->update(['envio_status' => 'erro']);
    }
}
