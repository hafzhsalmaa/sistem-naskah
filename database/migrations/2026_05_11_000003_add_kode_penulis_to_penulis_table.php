<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penulis', function (Blueprint $table): void {
            $table->string('kode_penulis', 32)->nullable()->after('id_penulis');
        });

        DB::table('penulis')
            ->select('id_penulis', 'jurusan_pendidikan', 'bidang_keahlian')
            ->orderBy('id_penulis')
            ->chunk(200, function ($penulisList): void {
                foreach ($penulisList as $penulis) {
                    DB::table('penulis')
                        ->where('id_penulis', $penulis->id_penulis)
                        ->update([
                            'kode_penulis' => $this->generateKodePenulis(
                                $penulis->jurusan_pendidikan ?: $penulis->bidang_keahlian,
                                (int) $penulis->id_penulis
                            ),
                        ]);
                }
            });

        Schema::table('penulis', function (Blueprint $table): void {
            $table->unique('kode_penulis');
        });
    }

    public function down(): void
    {
        Schema::table('penulis', function (Blueprint $table): void {
            $table->dropUnique('penulis_kode_penulis_unique');
            $table->dropColumn('kode_penulis');
        });
    }

    private function generateKodePenulis(?string $jurusan, int $idPenulis): string
    {
        return sprintf('P-%s-%03d', $this->jurusanCode($jurusan), $idPenulis);
    }

    private function jurusanCode(?string $jurusan): string
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
};
