<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('editor', function (Blueprint $table): void {
            $table->string('kode_editor', 32)->nullable()->after('id_editor');
        });

        DB::table('editor')
            ->select('id_editor', 'mata_pelajaran', 'bidang_keahlian')
            ->orderBy('id_editor')
            ->chunk(200, function ($editorList): void {
                foreach ($editorList as $editor) {
                    DB::table('editor')
                        ->where('id_editor', $editor->id_editor)
                        ->update([
                            'kode_editor' => $this->generateKodeEditor(
                                $editor->mata_pelajaran,
                                $editor->bidang_keahlian,
                                (int) $editor->id_editor
                            ),
                        ]);
                }
            });

        Schema::table('editor', function (Blueprint $table): void {
            $table->unique('kode_editor');
        });
    }

    public function down(): void
    {
        Schema::table('editor', function (Blueprint $table): void {
            $table->dropUnique('editor_kode_editor_unique');
            $table->dropColumn('kode_editor');
        });
    }

    private function generateKodeEditor(?string $mataPelajaran, ?string $bidangKeahlian, int $idEditor): string
    {
        return sprintf(
            'E-%s-%s-%03d',
            $this->mapelCode($mataPelajaran),
            $this->jenjangCode($bidangKeahlian),
            $idEditor
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
