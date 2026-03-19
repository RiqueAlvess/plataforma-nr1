<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unidade extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'codigo',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function setores(): HasMany
    {
        return $this->hasMany(Setor::class);
    }

    public function csvRecords(): HasMany
    {
        return $this->hasMany(CsvRecord::class);
    }

    public function leaderPermissions(): HasMany
    {
        return $this->hasMany(LeaderPermission::class);
    }
}
