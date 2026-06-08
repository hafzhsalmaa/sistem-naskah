<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DemoReplaceFiles extends Command
{
    protected $signature = 'demo:replace-files {--dry-run : Tampilkan rencana tanpa mengganti file atau update database}';

    protected $description = 'Replace uploaded demo file contents while keeping existing storage paths intact.';

    private const DOCX_MIME = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

    private array $dummyFiles = [
        'versi_naskah' => 'Naskah Buku Pelajaran.docx',
        'revisi' => 'Naskah Buku Review Editor.docx',
        'final_editor' => 'Naskah Buku Final Editor.docx',
        'layout' => 'Naskah Buku Layout Layouter.pdf',
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if (! $this->dummyFilesExist()) {
            return self::FAILURE;
        }

        $groups = $this->buildGroups();
        $this->reportPlan($groups);

        if ($dryRun) {
            $this->components->info('Dry-run selesai. Tidak ada file atau database yang diubah.');

            return self::SUCCESS;
        }

        $timestamp = now()->format('Ymd_His');
        $backupRoot = storage_path('app/backup-demo-files/'.$timestamp);

        File::ensureDirectoryExists($backupRoot.'/files');
        File::ensureDirectoryExists($backupRoot.'/database');

        $this->backupDatabase($backupRoot);
        $this->backupAndReplaceFiles($groups, $backupRoot);
        $this->updateDatabaseNames($groups);

        $this->components->info('Replace file demo selesai.');
        $this->line('Backup dibuat di: '.$backupRoot);
        $this->line('Path database dan nama file random storage tidak diubah.');

        return self::SUCCESS;
    }

    private function dummyFilesExist(): bool
    {
        $ok = true;

        foreach ($this->dummyFiles as $fileName) {
            $path = $this->dummyPath($fileName);

            if (! File::exists($path)) {
                $this->components->error('File dummy tidak ditemukan: '.$path);
                $ok = false;
            }
        }

        return $ok;
    }

    private function buildGroups(): array
    {
        $groups = [
            'versi_naskah' => [
                'label' => 'File naskah penulis',
                'table' => 'versi_naskah',
                'key' => 'id_versi',
                'path_field' => 'file_path',
                'name_field' => 'nama_file_asli',
                'dummy' => $this->dummyFiles['versi_naskah'],
                'updates' => ['nama_file_asli' => $this->dummyFiles['versi_naskah']],
                'records' => $this->records('versi_naskah', 'id_versi', 'file_path'),
            ],
            'revisi' => [
                'label' => 'Lampiran review editor',
                'table' => 'revisi',
                'key' => 'id_revisi',
                'path_field' => 'file_review_path',
                'name_field' => 'nama_file_review_asli',
                'dummy' => $this->dummyFiles['revisi'],
                'updates' => [
                    'nama_file_review_asli' => $this->dummyFiles['revisi'],
                    'file_review_mime' => self::DOCX_MIME,
                ],
                'records' => $this->records('revisi', 'id_revisi', 'file_review_path'),
            ],
            'final_editor' => [
                'label' => 'File final editor',
                'table' => 'naskah',
                'key' => 'id_naskah',
                'path_field' => 'file_final_editor_path',
                'name_field' => 'nama_file_final_editor_asli',
                'dummy' => $this->dummyFiles['final_editor'],
                'updates' => ['nama_file_final_editor_asli' => $this->dummyFiles['final_editor']],
                'records' => $this->records('naskah', 'id_naskah', 'file_final_editor_path'),
            ],
            'layout' => [
                'label' => 'File layout layouter',
                'table' => 'layout',
                'key' => 'id_layout',
                'path_field' => 'file_layout',
                'name_field' => Schema::hasColumn('layout', 'nama_file_layout_asli') ? 'nama_file_layout_asli' : null,
                'dummy' => $this->dummyFiles['layout'],
                'updates' => Schema::hasColumn('layout', 'nama_file_layout_asli')
                    ? ['nama_file_layout_asli' => $this->dummyFiles['layout']]
                    : [],
                'records' => $this->records('layout', 'id_layout', 'file_layout'),
            ],
        ];

        foreach ($groups as &$group) {
            $group['existing_files'] = $group['records']->filter(
                fn (object $record): bool => Storage::disk('local')->exists($record->{$group['path_field']})
            )->count();
            $group['missing_files'] = $group['records']->count() - $group['existing_files'];
        }

        return $groups;
    }

    private function records(string $table, string $keyField, string $pathField): Collection
    {
        return DB::table($table)
            ->whereNotNull($pathField)
            ->where($pathField, '<>', '')
            ->select($keyField, $pathField)
            ->orderBy($keyField)
            ->get();
    }

    private function reportPlan(array $groups): void
    {
        $rows = [];

        foreach ($groups as $group) {
            $rows[] = [
                $group['label'],
                $group['records']->count(),
                $group['existing_files'],
                $group['missing_files'],
                $group['dummy'],
                $group['updates'] === [] ? '-' : implode(', ', array_keys($group['updates'])),
            ];
        }

        $this->table(
            ['Kategori', 'Row DB', 'File ada', 'File hilang', 'Dummy', 'Field DB diupdate'],
            $rows
        );
    }

    private function backupDatabase(string $backupRoot): void
    {
        foreach (['versi_naskah', 'revisi', 'naskah', 'layout'] as $table) {
            $data = DB::table($table)->get();
            File::put(
                $backupRoot.'/database/'.$table.'.json',
                json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
        }
    }

    private function backupAndReplaceFiles(array $groups, string $backupRoot): void
    {
        foreach ($groups as $group) {
            $dummyPath = $this->dummyPath($group['dummy']);

            foreach ($group['records'] as $record) {
                $relativePath = $record->{$group['path_field']};

                if (! Storage::disk('local')->exists($relativePath)) {
                    $this->warn('Skip file hilang: '.$relativePath);

                    continue;
                }

                $sourcePath = Storage::disk('local')->path($relativePath);
                $backupPath = $backupRoot.'/files/'.$relativePath;

                File::ensureDirectoryExists(dirname($backupPath));
                File::copy($sourcePath, $backupPath);
                File::copy($dummyPath, $sourcePath);
            }
        }
    }

    private function updateDatabaseNames(array $groups): void
    {
        DB::transaction(function () use ($groups): void {
            foreach ($groups as $group) {
                if ($group['updates'] === []) {
                    continue;
                }

                $idsWithExistingFiles = $group['records']
                    ->filter(fn (object $record): bool => Storage::disk('local')->exists($record->{$group['path_field']}))
                    ->pluck($group['key'])
                    ->all();

                if ($idsWithExistingFiles === []) {
                    continue;
                }

                DB::table($group['table'])
                    ->whereIn($group['key'], $idsWithExistingFiles)
                    ->update($group['updates']);
            }
        });
    }

    private function dummyPath(string $fileName): string
    {
        return storage_path('app/dummy-files/'.$fileName);
    }
}
