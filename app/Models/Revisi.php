<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revisi extends Model
{
    protected $table = 'revisi';

    protected $primaryKey = 'id_revisi';

    public $timestamps = false;

    protected $fillable = [
        'id_naskah',
        'id_editor',
        'id_penulis',
        'catatan_editor',
        'catatan_penulis',
        'file_review_path',
        'nama_file_review_asli',
        'file_review_mime',
        'tanggal_revisi',
        'status_revisi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_revisi' => 'datetime',
        ];
    }

    public function naskah(): BelongsTo
    {
        return $this->belongsTo(Naskah::class, 'id_naskah', 'id_naskah');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(Editor::class, 'id_editor', 'id_editor');
    }

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(Penulis::class, 'id_penulis', 'id_penulis');
    }
}
