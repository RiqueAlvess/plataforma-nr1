<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\TenantUserRequest;
use App\Models\User;
use App\Services\TenantUserService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly TenantUserService $tenantUserService
    ) {}

    public function index(): Response
    {
        $usuarios = $this->tenantUserService->paginate(15);

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios,
        ]);
    }

    public function create(): Response
    {
        $unidades = $this->tenantUserService->obterUnidadesComSetores();

        return Inertia::render('Usuarios/Create', [
            'unidades' => $unidades,
        ]);
    }

    public function store(TenantUserRequest $request): RedirectResponse
    {
        $this->tenantUserService->criar($request->validated());

        return redirect()->route('tenant.usuarios.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $usuario): Response
    {
        $usuario->load('leaderPermissions.unidade', 'leaderPermissions.setor');
        $unidades = $this->tenantUserService->obterUnidadesComSetores();

        return Inertia::render('Usuarios/Edit', [
            'usuario' => $usuario,
            'unidades' => $unidades,
        ]);
    }

    public function update(TenantUserRequest $request, User $usuario): RedirectResponse
    {
        $this->tenantUserService->atualizar($usuario, $request->validated());

        return redirect()->route('tenant.usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        $this->tenantUserService->deletar($usuario);

        return redirect()->route('tenant.usuarios.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }

    public function toggleLock(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'Você não pode bloquear seu próprio usuário.');
        }

        $this->tenantUserService->alternarBloqueio($usuario);

        $mensagem = $usuario->fresh()->isLocked()
            ? 'Usuário bloqueado com sucesso.'
            : 'Usuário desbloqueado com sucesso.';

        return back()->with('success', $mensagem);
    }
}
