<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $query = User::with('tenant:id,company_name');

        if (request('tenant_id')) {
            $query->where('tenant_id', request('tenant_id'));
        }

        return Inertia::render('Admin/Users/Index', [
            'users' => $query->paginate(15)->withQueryString(),
            'tenants' => Tenant::all(['id', 'company_name']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'tenants' => Tenant::all(['id', 'company_name']),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['tenant_id'])) {
            $data['tenant_id'] = null;
        }

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->load('tenant:id,company_name'),
            'tenants' => Tenant::all(['id', 'company_name']),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['tenant_id'])) {
            $data['tenant_id'] = null;
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário removido com sucesso!');
    }

    public function toggleLock(User $user): RedirectResponse
    {
        if ($user->isLocked()) {
            $user->resetFailedAttempts();
            $message = 'Conta desbloqueada com sucesso!';
        } else {
            $user->update(['locked_at' => now()]);
            $message = 'Conta bloqueada com sucesso!';
        }

        return back()->with('success', $message);
    }
}
