<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'status',
    ];

    public function invites(): HasMany
    {
        return $this->hasMany(SurveyInvite::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function dimensionScores(): HasMany
    {
        return $this->hasMany(DimensionScore::class);
    }

    public function isAtiva(): bool
    {
        return $this->status === 'ativa';
    }

    public function totalConvidados(): int
    {
        return $this->invites()->count();
    }

    public function totalRespondidos(): int
    {
        return $this->invites()->where('resposta_status', 'respondido')->count();
    }

    public function taxaAdesao(): float
    {
        $total = $this->totalConvidados();
        if ($total === 0) {
            return 0.0;
        }

        return round(($this->totalRespondidos() / $total) * 100, 1);
    }
}
