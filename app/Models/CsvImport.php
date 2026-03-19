<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CsvImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nome_arquivo',
        'status',
        'total_registros',
        'registros_importados',
        'registros_com_erro',
        'mensagem_erro',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(CsvRecord::class);
    }

    public function isConcluido(): bool
    {
        return $this->status === 'concluido';
    }

    public function isProcessando(): bool
    {
        return $this->status === 'processando';
    }

    public function hasErro(): bool
    {
        return $this->status === 'erro';
    }
}
