<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Editor extends Model
{
    protected $table = 'editor';

    protected $primaryKey = 'id_editor';

    public $timestamps = false;

    protected $fillable = [
        'kode_editor',
        'id_user',
        'nama_lengkap',
        'no_hp',
        'bidang_keahlian',
        'kategori_mapel',
        'mata_pelajaran',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public static function generateKodeEditor(?string $mataPelajaran, ?string $bidangKeahlian, int $idEditor): string
    {
        return sprintf(
            'E-%s-%s-%03d',
            self::mapelCode($mataPelajaran),
            self::jenjangCode($bidangKeahlian),
            $idEditor
        );
    }

    private static function mapelCode(?string $mataPelajaran): string
    {
        $normalized = strtolower(trim((string) $mataPelajaran));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? '';

        $map = [
            'bahasa inggris' => 'BING',
            'bahasa indonesia' => 'BIND',
            'matematika' => 'MTK',
            'ipa' => 'IPA',
            'ips' => 'IPS',
            'pendidikan agama islam' => 'PAI',
            'agama' => 'PAI',
            'pendidikan kewarganegaraan' => 'PKN',
            'pkn' => 'PKN',
        ];

        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        $words = preg_split('/[^a-z0-9]+/i', trim((string) $mataPelajaran), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initials = collect($words)
            ->map(fn (string $word): string => strtoupper(substr($word, 0, 1)))
            ->take(5)
            ->implode('');

        if ($initials !== '') {
            return $initials;
        }

        $fallback = strtoupper(preg_replace('/[^a-z0-9]/i', '', (string) $mataPelajaran) ?? '');

        return substr($fallback !== '' ? $fallback : 'GEN', 0, 5);
    }

    private static function jenjangCode(?string $bidangKeahlian): string
    {
        $jenjang = strtoupper(preg_replace('/[^a-z0-9]/i', '', (string) $bidangKeahlian) ?? '');

        return $jenjang !== '' ? $jenjang : 'GEN';
    }
}
