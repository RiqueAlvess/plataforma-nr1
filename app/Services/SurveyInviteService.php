<?php

namespace App\Services;

use App\Jobs\SendSurveyInviteJob;
use App\Models\Campaign;
use App\Models\CsvImportRecord;
use App\Models\SurveyInvite;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class SurveyInviteService
{
    public function paginar(Campaign $campaign, int $perPage = 20): LengthAwarePaginator
    {
        return SurveyInvite::where('campaign_id', $campaign->id)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create invites for all CSV import records that do not yet have one in this campaign.
     */
    public function prepararConvites(Campaign $campaign): int
    {
        $existingHashes = SurveyInvite::where('campaign_id', $campaign->id)
            ->pluck('email_hash')
            ->flip()
            ->all();

        $registros = CsvImportRecord::whereNotNull('email_hash')
            ->select('email_hash')
            ->distinct()
            ->get();

        $criados = 0;

        foreach ($registros as $record) {
            $hash = $record->email_hash;

            if (isset($existingHashes[$hash])) {
                continue;
            }

            SurveyInvite::create([
                'campaign_id'     => $campaign->id,
                'email_hash'      => $hash,
                'token'           => Str::random(64),
                'envio_status'    => 'pendente',
                'resposta_status' => 'pendente',
            ]);

            $existingHashes[$hash] = true;
            $criados++;
        }

        return $criados;
    }

    /**
     * Dispatch email sending jobs for selected invites.
     *
     * @param  array<int>  $inviteIds
     */
    public function enviarConvites(Campaign $campaign, array $inviteIds): int
    {
        $invites = SurveyInvite::where('campaign_id', $campaign->id)
            ->whereIn('id', $inviteIds)
            ->where('resposta_status', 'pendente')
            ->get();

        $enfileirados = 0;

        foreach ($invites as $invite) {
            SendSurveyInviteJob::dispatch($invite);

            $invite->update([
                'envio_status' => 'enviado',
                'enviado_em'   => now(),
            ]);

            $enfileirados++;
        }

        return $enfileirados;
    }

    public function marcarRespondido(SurveyInvite $invite): void
    {
        $invite->update(['resposta_status' => 'respondido']);
    }

    public static function hashEmail(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }
}
