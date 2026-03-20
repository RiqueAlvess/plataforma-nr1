<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SurveyInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'email_hash',
        'token',
        'envio_status',
        'resposta_status',
        'enviado_em',
    ];

    protected $casts = [
        'enviado_em' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function response(): HasOne
    {
        return $this->hasOne(SurveyResponse::class);
    }

    public function foiRespondido(): bool
    {
        return $this->resposta_status === 'respondido';
    }

    public function podeReceberEnvio(): bool
    {
        return $this->resposta_status === 'pendente';
    }
}
