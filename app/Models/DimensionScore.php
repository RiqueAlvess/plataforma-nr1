<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DimensionScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_response_id',
        'campaign_id',
        'dimensao',
        'score',
        'dimensao_negativa',
        'score_risco',
        'probabilidade',
        'severidade',
        'nr',
        'classificacao_risco',
        'genero',
        'faixa_etaria',
        'unidade_id',
        'setor_id',
    ];

    protected $casts = [
        'dimensao_negativa' => 'boolean',
        'score' => 'float',
        'score_risco' => 'float',
    ];

    public function response(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class, 'survey_response_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
