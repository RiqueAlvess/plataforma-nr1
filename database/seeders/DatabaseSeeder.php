<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Global Admin (banco central)
        User::firstOrCreate(
            ['email' => 'admin@plataformanr1.com'],
            [
                'name'      => 'Administrador Global',
                'password'  => Hash::make('admin@123'),
                'role'      => UserRole::GLOBAL_ADMIN,
                'is_active' => true,
            ]
        );

        // Tenant de demo
        $tenant = Tenant::firstOrCreate(
            ['id' => 'demo-empresa'],
            [
                'company_name'      => 'Empresa Demo Ltda',
                'cnpj'              => '00.000.000/0001-00',
                'cnae'              => '7490-1/04',
                'responsible_email' => 'rh@demo.com',
                'is_active'         => true,
            ]
        );

        if ($tenant->domains()->count() === 0) {
            $tenant->domains()->create(['domain' => 'demo.localhost']);
        }

        // Executar no contexto do tenant para criar usuários no banco do tenant
        tenancy()->initialize($tenant);

        User::firstOrCreate(
            ['email' => 'rh@demo.com'],
            [
                'name'      => 'RH Demo',
                'password'  => Hash::make('rh@123'),
                'role'      => UserRole::RH,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'lider@demo.com'],
            [
                'name'      => 'Líder Demo',
                'password'  => Hash::make('lider@123'),
                'role'      => UserRole::LEADER,
                'is_active' => true,
            ]
        );

        tenancy()->end();

        $this->command->info('=== Seed concluído ===');
        $this->command->info('Admin Global: admin@plataformanr1.com / admin@123');
        $this->command->info('RH (no tenant demo.localhost): rh@demo.com / rh@123');
        $this->command->info('Líder (no tenant demo.localhost): lider@demo.com / lider@123');
    }
}
