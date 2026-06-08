<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penulis extends Model
{
    protected $table = 'penulis';

    protected $primaryKey = 'id_penulis';

    public $timestamps = false;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function naskah(): HasMany
    {
        return $this->hasMany(Naskah::class, 'id_penulis', 'id_penulis');
    }

    public static function generateKodePenulis(?string $jurusan, int $idPenulis): string
    {
        return sprintf('P-%s-%03d', self::jurusanCode($jurusan), $idPenulis);
    }

    private static function jurusanCode(?string $jurusan): string
    {
        $normalized = strtolower(trim((string) $jurusan));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? '';

        $map = [
            'sastra inggris' => 'SING',
            'teknik informatika' => 'TINF',
            'ilmu komputer' => 'ILKOM',
            'pendidikan matematika' => 'PMTK',
            'pendidikan bahasa indonesia' => 'PBIN',
            'pendidikan bahasa inggris' => 'PBING',
            'biologi' => 'BIO',
            'kimia' => 'KIM',
            'fisika' => 'FIS',
        ];

        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        $words = preg_split('/[^a-z0-9]+/i', trim((string) $jurusan), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initials = collect($words)
            ->map(fn (string $word): string => strtoupper(substr($word, 0, 1)))
            ->take(5)
            ->implode('');

        if ($initials !== '') {
            return $initials;
        }

        $fallback = strtoupper(preg_replace('/[^a-z0-9]/i', '', (string) $jurusan) ?? '');

        return substr($fallback !== '' ? $fallback : 'GEN', 0, 5);
    }
}
