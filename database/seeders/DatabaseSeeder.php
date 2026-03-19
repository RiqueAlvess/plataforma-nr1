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
        // Create Global Admin
        User::create([
            'name' => 'Administrador Global',
            'email' => 'admin@plataformanr1.com',
            'password' => Hash::make('admin@123'),
            'role' => UserRole::GLOBAL_ADMIN,
            'is_active' => true,
        ]);

        // Create demo tenant
        $tenant = Tenant::create([
            'id' => 'demo-empresa',
            'company_name' => 'Empresa Demo Ltda',
            'cnpj' => '00.000.000/0001-00',
            'cnae' => '7490-1/04',
            'responsible_email' => 'rh@demo.com',
            'is_active' => true,
        ]);

        $tenant->domains()->create([
            'domain' => 'demo.localhost',
        ]);

        $this->command->info('Seeding complete!');
        $this->command->info('Global Admin: admin@plataformanr1.com / admin@123');
    }
}
