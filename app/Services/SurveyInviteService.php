<?php

namespace App\Services;

use App\Jobs\SendSurveyInviteEmail;
use App\Models\Campaign;
use App\Models\CsvRecord;
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
     * Create invites for all CSV records that do not yet have one in this campaign.
     */
    public function prepararConvites(Campaign $campaign): int
    {
        $existingHashes = SurveyInvite::where('campaign_id', $campaign->id)
            ->pluck('email_hash')
            ->flip()
            ->all();

        $registros = CsvRecord::whereNotNull('email_hash')
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
                'campaign_id'    => $campaign->id,
                'email_hash'     => $hash,
                'token'          => Str::random(64),
                'envio_status'   => 'pendente',
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

        foreach ($invites as $invite) {
            // Find the real email associated with this hash to dispatch the job
            $csvRecord = CsvRecord::where('email_hash', $invite->email_hash)->first();

            if (! $csvRecord) {
                continue;
            }

            SendSurveyInviteEmail::dispatch($invite, $csvRecord->email, $campaign);
        }

        return $invites->count();
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
