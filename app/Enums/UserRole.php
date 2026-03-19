<?php

namespace App\Enums;

enum UserRole: string
{
    case GLOBAL_ADMIN = 'GLOBAL_ADMIN';
    case RH = 'RH';
    case LEADER = 'LEADER';

    public function label(): string
    {
        return match($this) {
            UserRole::GLOBAL_ADMIN => 'Administrador Global',
            UserRole::RH => 'Recursos Humanos',
            UserRole::LEADER => 'Líder',
        };
    }

    public function canAccessAdminPanel(): bool
    {
        return $this === UserRole::GLOBAL_ADMIN;
    }

    public function canManageTenant(): bool
    {
        return $this === UserRole::RH;
    }
}
