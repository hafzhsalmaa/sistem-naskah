<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('layouter', function (Blueprint $table): void {
            $table->string('kode_layouter', 32)->nullable()->after('id_layouter');
        });

        DB::table('layouter')
            ->select('id_layouter', 'mata_pelajaran', 'bidang_keahlian')
            ->orderBy('id_layouter')
            ->chunk(200, function ($layouterList): void {
                foreach ($layouterList as $layouter) {
                    DB::table('layouter')
                        ->where('id_layouter', $layouter->id_layouter)
                        ->update([
                            'kode_layouter' => $this->generateKodeLayouter(
                                $layouter->mata_pelajaran,
                                $layouter->bidang_keahlian,
                                (int) $layouter->id_layouter
                            ),
                        ]);
                }
            });

        Schema::table('layouter', function (Blueprint $table): void {
            $table->unique('kode_layouter');
        });
    }

    public function down(): void
    {
        Schema::table('layouter', function (Blueprint $table): void {
            $table->dropUnique('layouter_kode_layouter_unique');
            $table->dropColumn('kode_layouter');
        });
    }

    private function generateKodeLayouter(?string $mataPelajaran, ?string $bidangKeahlian, int $idLayouter): string
    {
        return sprintf(
            'L-%s-%s-%03d',
            $this->mapelCode($mataPelajaran),
            $this->jenjangCode($bidangKeahlian),
            $idLayouter
        );
    }

    private function mapelCode(?string $mataPelajaran): string
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

    private function jenjangCode(?string $bidangKeahlian): string
    {
        $jenjang = strtoupper(preg_replace('/[^a-z0-9]/i', '', (string) $bidangKeahlian) ?? '');

        return $jenjang !== '' ? $jenjang : 'GEN';
    }
};
