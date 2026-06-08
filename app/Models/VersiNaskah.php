<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VersiNaskah extends Model
{
    protected $table = 'versi_naskah';

    protected $primaryKey = 'id_versi';

    public $timestamps = false;

    protected $fillable = [
        'id_naskah',
        'file_path',
        'nama_file_asli',
        'no_versi',
        'tanggal_upload',
        'id_user_pengunggah',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_upload' => 'datetime',
        ];
    }

    public function naskah(): BelongsTo
    {
        return $this->belongsTo(Naskah::class, 'id_naskah', 'id_naskah');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_pengunggah', 'id_user');
    }
}
