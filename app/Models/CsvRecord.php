<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CsvRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'csv_import_id',
        'unidade_id',
        'setor_id',
        'email',
        'linha_csv',
    ];

    public function csvImport(): BelongsTo
    {
        return $this->belongsTo(CsvImport::class);
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
