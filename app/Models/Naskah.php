<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Naskah extends Model
{
    protected $table = 'naskah';

    protected $primaryKey = 'id_naskah';

    public $timestamps = false;

    protected $fillable = [
        'kode_naskah',
        'id_penulis',
        'judul',
        'kelas',
        'bidang_keahlian',
        'kurikulum',
        'kategori_mapel',
        'mata_pelajaran',
        'deskripsi',
        'cek_kurikulum',
        'cek_silabus',
        'cek_rpp',
        'bebas_sara',
        'tanggal_submit',
        'status_naskah',
        'file_final_editor_path',
        'nama_file_final_editor_asli',
        'tanggal_file_final_editor',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_submit' => 'datetime',
            'tanggal_file_final_editor' => 'datetime',
            'cek_kurikulum' => 'boolean',
            'cek_silabus' => 'boolean',
            'cek_rpp' => 'boolean',
            'bebas_sara' => 'boolean',
        ];
    }

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(Penulis::class, 'id_penulis', 'id_penulis');
    }

    public function versiNaskah(): HasMany
    {
        return $this->hasMany(VersiNaskah::class, 'id_naskah', 'id_naskah');
    }

    public static function generateKodeNaskah(?string $mataPelajaran, ?string $kelas, int $idNaskah): string
    {
        return sprintf(
            'N-%s-%s-%04d',
            self::mapelCode($mataPelajaran),
            self::kelasCode($kelas),
            $idNaskah
        );
    }

    private static function mapelCode(?string $mataPelajaran): string
    {
        $normalized = strtolower(trim((string) $mataPelajaran));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? '';

        $map = [
            'ipa' => 'IPA',
            'ilmu pengetahuan alam' => 'IPA',
            'matematika' => 'MTK',
            'bahasa indonesia' => 'BIN',
            'bahasa inggris' => 'BIG',
            'pendidikan agama islam' => 'PAI',
            'pendidikan kewarganegaraan' => 'PKN',
            'pkn' => 'PKN',
        ];

        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        $words = preg_split('/[^a-z0-9]+/i', trim((string) $mataPelajaran), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initials = collect($words)
            ->map(fn (string $word): string => strtoupper(substr($word, 0, 1)))
            ->take(4)
            ->implode('');

        if ($initials !== '') {
            return $initials;
        }

        $fallback = strtoupper(preg_replace('/[^a-z0-9]/i', '', (string) $mataPelajaran) ?? '');

        return substr($fallback !== '' ? $fallback : 'UMM', 0, 4);
    }

    private static function kelasCode(?string $kelas): string
    {
        preg_match('/\d+/', (string) $kelas, $matches);
        $value = isset($matches[0]) ? (int) $matches[0] : 0;

        return str_pad((string) $value, 2, '0', STR_PAD_LEFT);
    }
}
