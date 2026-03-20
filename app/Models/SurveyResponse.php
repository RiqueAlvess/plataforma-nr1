<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'survey_invite_id',
        'genero',
        'faixa_etaria',
        'consentimento_aceito',
        'respondido_em',
    ];

    protected $casts = [
        'consentimento_aceito' => 'boolean',
        'respondido_em' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function invite(): BelongsTo
    {
        return $this->belongsTo(SurveyInvite::class, 'survey_invite_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ResponseAnswer::class);
    }

    public function dimensionScores(): HasMany
    {
        return $this->hasMany(DimensionScore::class);
    }
}
