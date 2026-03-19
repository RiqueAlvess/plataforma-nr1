<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CampaignRequest;
use App\Models\Campaign;
use App\Services\AnalyticsService;
use App\Services\CampaignService;
use App\Services\SurveyInviteService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CampaignController extends Controller
{
    public function __construct(
        private readonly CampaignService $campaignService,
        private readonly SurveyInviteService $inviteService,
        private readonly AnalyticsService $analyticsService
    ) {}

    public function index(): Response
    {
        $campaigns = $this->campaignService->paginate(15);

        return Inertia::render('Campanhas/Index', [
            'campaigns' => $campaigns,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Campanhas/Create');
    }

    public function store(CampaignRequest $request): RedirectResponse
    {
        $campaign = $this->campaignService->criar($request->validated());

        return redirect()->route('tenant.campanhas.show', $campaign)
            ->with('success', 'Campanha criada com sucesso.');
    }

    public function show(Campaign $campaign): Response
    {
        $invites = $this->inviteService->paginar($campaign, 20);

        return Inertia::render('Campanhas/Show', [
            'campaign' => $campaign->loadCount(['invites', 'responses']),
            'invites'  => $invites,
            'metricas' => [
                'total_convidados'  => $campaign->totalConvidados(),
                'total_respondidos' => $campaign->totalRespondidos(),
                'taxa_adesao'       => $campaign->taxaAdesao(),
            ],
        ]);
    }

    public function edit(Campaign $campaign): Response
    {
        return Inertia::render('Campanhas/Edit', [
            'campaign' => $campaign,
        ]);
    }

    public function update(CampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $this->campaignService->atualizar($campaign, $request->validated());

        return redirect()->route('tenant.campanhas.show', $campaign)
            ->with('success', 'Campanha atualizada com sucesso.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $this->campaignService->deletar($campaign);

        return redirect()->route('tenant.campanhas.index')
            ->with('success', 'Campanha excluída com sucesso.');
    }

    public function toggleStatus(Campaign $campaign): RedirectResponse
    {
        if ($campaign->status === 'ativa') {
            $this->campaignService->encerrar($campaign);
            $message = 'Campanha encerrada.';
        } else {
            $this->campaignService->ativar($campaign);
            $message = 'Campanha ativada.';
        }

        return back()->with('success', $message);
    }

    public function analytics(Campaign $campaign): Response
    {
        $data = $this->analyticsService->getDashboardData($campaign);

        return Inertia::render('Campanhas/Analytics', [
            'campaign'  => $campaign,
            'analytics' => $data,
        ]);
    }
}
