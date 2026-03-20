<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Services\SurveyInviteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SurveyInviteController extends Controller
{
    public function __construct(
        private readonly SurveyInviteService $inviteService
    ) {}

    /**
     * Prepare invites from imported CSV records.
     */
    public function preparar(Campaign $campaign): RedirectResponse
    {
        if (! $campaign->isAtiva()) {
            return back()->with('error', 'A campanha precisa estar ativa para preparar convites.');
        }

        $criados = $this->inviteService->prepararConvites($campaign);

        return back()->with('success', "{$criados} novo(s) convite(s) preparado(s).");
    }

    /**
     * Send survey invites to selected records.
     */
    public function enviar(Request $request, Campaign $campaign): RedirectResponse
    {
        $request->validate([
            'invite_ids'   => ['required', 'array', 'min:1'],
            'invite_ids.*' => ['integer'],
        ]);

        if (! $campaign->isAtiva()) {
            return back()->with('error', 'A campanha precisa estar ativa para enviar convites.');
        }

        $enviados = $this->inviteService->enviarConvites($campaign, $request->invite_ids);

        return back()->with('success', "{$enviados} convite(s) adicionado(s) à fila de envio.");
    }
}
