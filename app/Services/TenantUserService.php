<?php

namespace App\Services;

use App\Models\LeaderPermission;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class TenantUserService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::with('leaderPermissions.unidade', 'leaderPermissions.setor')
            ->whereIn('role', ['RH', 'LEADER'])
            ->latest()
            ->paginate($perPage);
    }

    public function criar(array $dados): User
    {
        $usuario = User::create([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'password' => Hash::make($dados['password']),
            'role' => $dados['role'],
            'is_active' => $dados['is_active'] ?? true,
        ]);

        if ($usuario->isLeader() && !empty($dados['permissoes'])) {
            $this->sincronizarPermissoes($usuario, $dados['permissoes']);
        }

        return $usuario->load('leaderPermissions.unidade', 'leaderPermissions.setor');
    }

    public function atualizar(User $usuario, array $dados): User
    {
        $camposAtualizar = [
            'name' => $dados['name'],
            'email' => $dados['email'],
            'role' => $dados['role'],
            'is_active' => $dados['is_active'] ?? $usuario->is_active,
        ];

        if (!empty($dados['password'])) {
            $camposAtualizar['password'] = Hash::make($dados['password']);
        }

        $usuario->update($camposAtualizar);

        if ($usuario->fresh()->isLeader()) {
            $this->sincronizarPermissoes($usuario, $dados['permissoes'] ?? []);
        } else {
            // Remove permissões se o role mudou para RH
            $usuario->leaderPermissions()->delete();
        }

        return $usuario->fresh(['leaderPermissions.unidade', 'leaderPermissions.setor']);
    }

    public function deletar(User $usuario): void
    {
        $usuario->leaderPermissions()->delete();
        $usuario->delete();
    }

    public function alternarBloqueio(User $usuario): void
    {
        if ($usuario->isLocked()) {
            $usuario->resetFailedAttempts();
        } else {
            $usuario->update(['locked_at' => now()]);
        }
    }

    private function sincronizarPermissoes(User $usuario, array $permissoes): void
    {
        $usuario->leaderPermissions()->delete();

        foreach ($permissoes as $permissao) {
            if (empty($permissao['unidade_id'])) {
                continue;
            }

            LeaderPermission::create([
                'user_id' => $usuario->id,
                'unidade_id' => $permissao['unidade_id'],
                'setor_id' => $permissao['setor_id'] ?? null,
            ]);
        }
    }

    public function obterUnidadesComSetores(): \Illuminate\Database\Eloquent\Collection
    {
        return Unidade::with('setores')
            ->where('is_active', true)
            ->orderBy('nome')
            ->get();
    }
}
