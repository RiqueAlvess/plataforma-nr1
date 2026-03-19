<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'unidade_id',
        'setor_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unidade(): BelongsTo
    {
        return $this->belongsTo(Unidade::class);
    }

    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }
}
