<?php

namespace App\Services;

use App\Models\Campaign;
use Illuminate\Pagination\LengthAwarePaginator;

class CampaignService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Campaign::withCount(['invites', 'responses'])
            ->latest()
            ->paginate($perPage);
    }

    public function criar(array $dados): Campaign
    {
        return Campaign::create([
            'nome'     => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
            'status'   => $dados['status'] ?? 'rascunho',
        ]);
    }

    public function atualizar(Campaign $campaign, array $dados): Campaign
    {
        $campaign->update([
            'nome'      => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
            'status'    => $dados['status'],
        ]);

        return $campaign->fresh();
    }

    public function encerrar(Campaign $campaign): Campaign
    {
        $campaign->update(['status' => 'encerrada']);

        return $campaign->fresh();
    }

    public function ativar(Campaign $campaign): Campaign
    {
        $campaign->update(['status' => 'ativa']);

        return $campaign->fresh();
    }

    public function deletar(Campaign $campaign): void
    {
        $campaign->delete();
    }
}
