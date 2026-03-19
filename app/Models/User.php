<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserRole;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'failed_login_attempts',
        'locked_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'locked_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'role' => UserRole::class,
        ];
    }

    public function isGlobalAdmin(): bool
    {
        return $this->role === UserRole::GLOBAL_ADMIN;
    }

    public function isRh(): bool
    {
        return $this->role === UserRole::RH;
    }

    public function isLeader(): bool
    {
        return $this->role === UserRole::LEADER;
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_login_attempts');
        if ($this->failed_login_attempts >= 3) {
            $this->update(['locked_at' => now()]);
        }
    }

    public function resetFailedAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_at' => null,
        ]);
    }
}
