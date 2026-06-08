<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('naskah', function (Blueprint $table): void {
            $table->string('kode_naskah', 32)->nullable()->after('id_naskah');
        });

        DB::table('naskah')
            ->select('id_naskah', 'mata_pelajaran', 'kelas')
            ->orderBy('id_naskah')
            ->chunk(200, function ($naskahList): void {
                foreach ($naskahList as $naskah) {
                    DB::table('naskah')
                        ->where('id_naskah', $naskah->id_naskah)
                        ->update([
                            'kode_naskah' => $this->generateKodeNaskah(
                                $naskah->mata_pelajaran,
                                $naskah->kelas,
                                (int) $naskah->id_naskah
                            ),
                        ]);
                }
            });

        Schema::table('naskah', function (Blueprint $table): void {
            $table->unique('kode_naskah');
        });
    }

    public function down(): void
    {
        Schema::table('naskah', function (Blueprint $table): void {
            $table->dropUnique('naskah_kode_naskah_unique');
            $table->dropColumn('kode_naskah');
        });
    }

    private function generateKodeNaskah(?string $mataPelajaran, ?string $kelas, int $idNaskah): string
    {
        return sprintf(
            'N-%s-%s-%04d',
            $this->mapelCode($mataPelajaran),
            $this->kelasCode($kelas),
            $idNaskah
        );
    }

    private function mapelCode(?string $mataPelajaran): string
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

    private function kelasCode(?string $kelas): string
    {
        preg_match('/\d+/', (string) $kelas, $matches);
        $value = isset($matches[0]) ? (int) $matches[0] : 0;

        return str_pad((string) $value, 2, '0', STR_PAD_LEFT);
    }
};
