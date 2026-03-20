<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CsvImportRecord extends Model
{
    use HasFactory;

    protected $table = 'csv_records';

    protected $fillable = [
        'csv_import_id',
        'unidade_id',
        'setor_id',
        'email_hash',
        'linha_csv',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(CsvImport::class, 'csv_import_id');
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
