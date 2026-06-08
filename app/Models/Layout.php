<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Layout extends Model
{
    protected $table = 'layout';

    protected $primaryKey = 'id_layout';

    public $timestamps = false;

    protected $fillable = [
        'id_naskah',
        'id_layouter',
        'id_penulis',
        'file_layout',
        'nama_file_layout_asli',
        'tanggal_layout',
        'status_layout',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_layout' => 'datetime',
        ];
    }

    public function naskah(): BelongsTo
    {
        return $this->belongsTo(Naskah::class, 'id_naskah', 'id_naskah');
    }

    public function layouter(): BelongsTo
    {
        return $this->belongsTo(Layouter::class, 'id_layouter', 'id_layouter');
    }
}
