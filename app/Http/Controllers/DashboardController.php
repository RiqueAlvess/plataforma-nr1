<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\LeaderPermission;
use App\Models\Unidade;
use App\Models\Setor;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analyticsService
    ) {}

    public function index(Request $request): Response
    {
        $user = auth()->user();

        if ($user->isRh()) {
            return $this->rhDashboard($request, $user);
        }

        if ($user->isLeader()) {
            return $this->leaderDashboard($request, $user);
        }

        // GLOBAL_ADMIN: simple welcome dashboard
        return Inertia::render('Dashboard/Index', [
            'stats' => [
                'role'            => $user->role,
                'welcome_message' => 'Bem-vindo ao Painel, ' . $user->name . '!',
            ],
        ]);
    }

    private function rhDashboard(Request $request, $user): Response
    {
        $unidadeId = $request->integer('unidade_id') ?: null;
        $setorId   = $request->integer('setor_id') ?: null;

        $unidades  = Unidade::where('is_active', true)->orderBy('nome')->get(['id', 'nome', 'codigo']);
        $setores   = $setorId === null && $unidadeId
            ? Setor::where('unidade_id', $unidadeId)->where('is_active', true)->orderBy('nome')->get(['id', 'nome'])
            : Setor::where('is_active', true)->orderBy('nome')->get(['id', 'nome', 'unidade_id']);

        $analytics = $this->analyticsService->getAggregateDashboardData(
            tenantId: 0, // tenant context already scoped by DB connection
            unidadeId: $unidadeId,
            setorId: $setorId,
        );

        $ultimaCampanha = Campaign::orderByDesc('created_at')->first(['id', 'nome', 'status']);

        return Inertia::render('Dashboard/Rh', [
            'analytics'       => $analytics,
            'unidades'        => $unidades,
            'setores'         => $setores,
            'filtros'         => ['unidade_id' => $unidadeId, 'setor_id' => $setorId],
            'ultima_campanha' => $ultimaCampanha,
        ]);
    }

    private function leaderDashboard(Request $request, $user): Response
    {
        // Load leader's permissions
        $permissions = LeaderPermission::where('user_id', $user->id)
            ->with(['unidade:id,nome,codigo', 'setor:id,nome'])
            ->get();

        $allowedUnidadeIds = $permissions->pluck('unidade_id')->filter()->unique()->values()->toArray();
        $allowedSetorIds   = $permissions->pluck('setor_id')->filter()->unique()->values()->toArray();

        // Filter request scoped to leader's permissions
        $unidadeId = $request->integer('unidade_id') ?: null;
        $setorId   = $request->integer('setor_id') ?: null;

        // Enforce: leader can only filter within allowed scope
        if ($unidadeId !== null && !in_array($unidadeId, $allowedUnidadeIds)) {
            $unidadeId = null;
        }
        if ($setorId !== null && !in_array($setorId, $allowedSetorIds)) {
            $setorId = null;
        }

        $unidades = Unidade::whereIn('id', $allowedUnidadeIds)->where('is_active', true)->orderBy('nome')->get(['id', 'nome', 'codigo']);
        $setores  = Setor::whereIn('id', $allowedSetorIds)->where('is_active', true)->orderBy('nome')->get(['id', 'nome', 'unidade_id']);

        $analytics = $this->analyticsService->getAggregateDashboardData(
            tenantId: 0,
            unidadeId: $unidadeId,
            setorId: $setorId,
            allowedUnidadeIds: count($allowedUnidadeIds) > 0 ? $allowedUnidadeIds : null,
            allowedSetorIds: count($allowedSetorIds) > 0 ? $allowedSetorIds : null,
        );

        return Inertia::render('Dashboard/Leader', [
            'analytics'   => $analytics,
            'unidades'    => $unidades,
            'setores'     => $setores,
            'filtros'     => ['unidade_id' => $unidadeId, 'setor_id' => $setorId],
            'permissoes'  => $permissions->map(fn ($p) => [
                'unidade' => $p->unidade?->nome,
                'setor'   => $p->setor?->nome,
            ]),
        ]);
    }
}
