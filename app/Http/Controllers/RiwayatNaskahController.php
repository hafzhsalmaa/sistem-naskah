<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\Revisi;
use App\Models\VersiNaskah;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RiwayatNaskahController extends Controller
{
    public function penulisIndex(Request $request): View
    {
        $penulis = $request->user()->penulis;

        abort_unless($penulis, 403);

        $riwayatList = $this->baseHistoryQuery()
            ->where('naskah.id_penulis', $penulis->getKey())
            ->whereNotNull('jadwal_penerbitan.tanggal_cetak')
            ->get();

        return view('penulis.riwayat-naskah.index', compact('riwayatList'));
    }

    public function editorIndex(Request $request): View
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $riwayatList = $this->baseHistoryQuery()
            ->where('naskah.id_editor', $editor->getKey())
            ->where(function ($query): void {
                $query->whereIn('naskah.status_naskah', [
                    'Menunggu Layout',
                    'Proses Layout',
                    'Revisi Layout',
                    'Selesai Layout',
                ])->orWhereNotNull('jadwal_penerbitan.tanggal_cetak');
            })
            ->get();

        return view('editor.riwayat-naskah.index', compact('riwayatList'));
    }

    public function layouterIndex(Request $request): View
    {
        $layouter = $request->user()->layouter;

        abort_unless($layouter, 403);

        $riwayatList = $this->baseHistoryQuery()
            ->where('naskah.id_layouter', $layouter->getKey())
            ->where('naskah.status_naskah', 'Selesai Layout')
            ->get();

        return view('layouter.riwayat-naskah.index', compact('riwayatList'));
    }

    public function penulisShow(Request $request, int $id): View
    {
        $penulis = $request->user()->penulis;

        abort_unless($penulis, 403);

        $naskah = $this->getHistoryNaskah()
            ->where('naskah.id_penulis', $penulis->getKey())
            ->where('naskah.id_naskah', $id)
            ->whereNotNull('jadwal_penerbitan.tanggal_cetak')
            ->firstOrFail();

        $historyItems = $this->buildHistoryItems($id);
        $revisiList = Revisi::query()
            ->where('id_naskah', $naskah->id_naskah)
            ->orderByDesc('tanggal_revisi')
            ->get();
        $downloadRoute = 'penulis.riwayat-naskah.download';

        return view('penulis.riwayat-naskah.show', compact('naskah', 'historyItems', 'revisiList', 'downloadRoute'));
    }

    public function editorShow(Request $request, int $id): View
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $naskah = $this->getHistoryNaskah()
            ->where('naskah.id_editor', $editor->getKey())
            ->where('naskah.id_naskah', $id)
            ->firstOrFail();

        $historyItems = $this->buildHistoryItems($id);
        $revisiList = Revisi::query()
            ->where('id_naskah', $naskah->id_naskah)
            ->orderByDesc('tanggal_revisi')
            ->get();
        $downloadRoute = 'editor.riwayat-naskah.download';

        return view('editor.riwayat-naskah.show', compact('naskah', 'historyItems', 'revisiList', 'downloadRoute'));
    }

    public function layouterShow(Request $request, int $id): View
    {
        $layouter = $request->user()->layouter;

        abort_unless($layouter, 403);

        $naskah = $this->getHistoryNaskah()
            ->where('naskah.id_layouter', $layouter->getKey())
            ->where('naskah.id_naskah', $id)
            ->where('naskah.status_naskah', 'Selesai Layout')
            ->firstOrFail();

        $historyItems = $this->buildHistoryItems($id);
        $downloadRoute = 'layouter.riwayat-naskah.download';

        return view('layouter.riwayat-naskah.show', compact('naskah', 'historyItems', 'downloadRoute'));
    }

    public function penulisDownload(Request $request, int $id): StreamedResponse
    {
        $penulis = $request->user()->penulis;

        abort_unless($penulis, 403);

        $this->getHistoryNaskah()
            ->where('naskah.id_penulis', $penulis->getKey())
            ->where('naskah.id_naskah', $id)
            ->whereNotNull('jadwal_penerbitan.tanggal_cetak')
            ->firstOrFail();

        return $this->downloadHistoryFile($request, $id);
    }

    public function penulisPreview(Request $request, int $id): BinaryFileResponse
    {
        $penulis = $request->user()->penulis;

        abort_unless($penulis, 403);

        $this->getHistoryNaskah()
            ->where('naskah.id_penulis', $penulis->getKey())
            ->where('naskah.id_naskah', $id)
            ->whereNotNull('jadwal_penerbitan.tanggal_cetak')
            ->firstOrFail();

        return $this->previewHistoryFile($request, $id);
    }

    public function editorDownload(Request $request, int $id): StreamedResponse
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $this->getHistoryNaskah()
            ->where('naskah.id_editor', $editor->getKey())
            ->where('naskah.id_naskah', $id)
            ->firstOrFail();

        return $this->downloadHistoryFile($request, $id);
    }

    public function editorPreview(Request $request, int $id): BinaryFileResponse
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $this->getHistoryNaskah()
            ->where('naskah.id_editor', $editor->getKey())
            ->where('naskah.id_naskah', $id)
            ->firstOrFail();

        return $this->previewHistoryFile($request, $id);
    }

    public function layouterDownload(Request $request, int $id): StreamedResponse
    {
        $layouter = $request->user()->layouter;

        abort_unless($layouter, 403);

        $this->getHistoryNaskah()
            ->where('naskah.id_layouter', $layouter->getKey())
            ->where('naskah.id_naskah', $id)
            ->where('naskah.status_naskah', 'Selesai Layout')
            ->firstOrFail();

        return $this->downloadHistoryFile($request, $id);
    }

    public function layouterPreview(Request $request, int $id): BinaryFileResponse
    {
        $layouter = $request->user()->layouter;

        abort_unless($layouter, 403);

        $this->getHistoryNaskah()
            ->where('naskah.id_layouter', $layouter->getKey())
            ->where('naskah.id_naskah', $id)
            ->where('naskah.status_naskah', 'Selesai Layout')
            ->firstOrFail();

        return $this->previewHistoryFile($request, $id);
    }

    private function baseHistoryQuery()
    {
        return DB::table('naskah')
            ->join('penulis', 'penulis.id_penulis', '=', 'naskah.id_penulis')
            ->join('users as penulis_user', 'penulis_user.id_user', '=', 'penulis.id_user')
            ->leftJoin('editor', 'editor.id_editor', '=', 'naskah.id_editor')
            ->leftJoin('users as editor_user', 'editor_user.id_user', '=', 'editor.id_user')
            ->leftJoin('layouter', 'layouter.id_layouter', '=', 'naskah.id_layouter')
            ->leftJoin('users as layouter_user', 'layouter_user.id_user', '=', 'layouter.id_user')
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->select(
                'naskah.id_naskah',
                'naskah.kode_naskah',
                'naskah.judul',
                'naskah.status_naskah',
                'penulis_user.username as nama_penulis',
                'editor_user.username as nama_editor',
                'layouter_user.username as nama_layouter',
                'jadwal_penerbitan.tanggal_cetak'
            )
            ->orderByDesc('naskah.id_naskah');
    }

    private function getHistoryNaskah()
    {
        return DB::table('naskah')
            ->join('penulis', 'penulis.id_penulis', '=', 'naskah.id_penulis')
            ->join('users as penulis_user', 'penulis_user.id_user', '=', 'penulis.id_user')
            ->leftJoin('editor', 'editor.id_editor', '=', 'naskah.id_editor')
            ->leftJoin('users as editor_user', 'editor_user.id_user', '=', 'editor.id_user')
            ->leftJoin('layouter', 'layouter.id_layouter', '=', 'naskah.id_layouter')
            ->leftJoin('users as layouter_user', 'layouter_user.id_user', '=', 'layouter.id_user')
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->select(
                'naskah.id_naskah',
                'naskah.kode_naskah',
                'naskah.judul',
                'naskah.kelas',
                'naskah.kurikulum',
                'naskah.kategori_mapel',
                'naskah.mata_pelajaran',
                'naskah.deskripsi',
                'naskah.status_naskah',
                'penulis_user.username as nama_penulis',
                'editor_user.username as nama_editor',
                'layouter_user.username as nama_layouter',
                'jadwal_penerbitan.tanggal_cetak'
            );
    }

    private function buildHistoryItems(int $idNaskah): Collection
    {
        $versiItems = VersiNaskah::query()
            ->leftJoin('users', 'users.id_user', '=', 'versi_naskah.id_user_pengunggah')
            ->where('versi_naskah.id_naskah', $idNaskah)
            ->orderBy('versi_naskah.no_versi')
            ->select(
                DB::raw("'versi' as source"),
                'versi_naskah.id_versi as ref_id',
                'versi_naskah.no_versi as nomor_versi',
                DB::raw("CONCAT('Versi ', versi_naskah.no_versi) as label_versi"),
                DB::raw("'Naskah Penulis' as jenis_file"),
                'users.username as uploader',
                'versi_naskah.tanggal_upload as tanggal_upload',
                'versi_naskah.file_path as file_path',
                'versi_naskah.nama_file_asli as nama_file_asli'
            )
            ->get();

        $layoutItems = Layout::query()
            ->leftJoin('layouter', 'layouter.id_layouter', '=', 'layout.id_layouter')
            ->leftJoin('users', 'users.id_user', '=', 'layouter.id_user')
            ->where('layout.id_naskah', $idNaskah)
            ->orderBy('layout.id_layout')
            ->select(
                DB::raw("'layout' as source"),
                'layout.id_layout as ref_id',
                DB::raw('9999 as nomor_versi'),
                DB::raw("'Versi Layout' as label_versi"),
                DB::raw("'Hasil Layout' as jenis_file"),
                'users.username as uploader',
                'layout.tanggal_layout as tanggal_upload',
                'layout.file_layout as file_path',
                'layout.nama_file_layout_asli as nama_file_asli'
            )
            ->get();

        $finalEditorItems = DB::table('naskah')
            ->leftJoin('editor as assigned_editor', 'assigned_editor.id_editor', '=', 'naskah.id_editor')
            ->leftJoin('users as assigned_editor_user', 'assigned_editor_user.id_user', '=', 'assigned_editor.id_user')
            ->where('naskah.id_naskah', $idNaskah)
            ->whereNotNull('naskah.file_final_editor_path')
            ->whereNotNull('naskah.nama_file_final_editor_asli')
            ->whereNotNull('naskah.tanggal_file_final_editor')
            ->select(
                DB::raw("'final_editor' as source"),
                'naskah.id_naskah as ref_id',
                DB::raw('9000 as nomor_versi'),
                DB::raw("'Versi Final Editor' as label_versi"),
                DB::raw("'File Final' as jenis_file"),
                DB::raw("COALESCE(assigned_editor_user.username, 'Editor') as uploader"),
                'naskah.tanggal_file_final_editor as tanggal_upload',
                'naskah.file_final_editor_path as file_path',
                'naskah.nama_file_final_editor_asli as nama_file_asli'
            )
            ->get();

        $reviewItems = Revisi::query()
            ->leftJoin('editor', 'editor.id_editor', '=', 'revisi.id_editor')
            ->leftJoin('users', 'users.id_user', '=', 'editor.id_user')
            ->where('revisi.id_naskah', $idNaskah)
            ->whereNotNull('revisi.file_review_path')
            ->select(
                DB::raw("'review_attachment' as source"),
                'revisi.id_revisi as ref_id',
                DB::raw('0 as nomor_versi'),
                DB::raw("'Review Editor' as label_versi"),
                DB::raw("'Lampiran Revisi' as jenis_file"),
                'users.username as uploader',
                'revisi.tanggal_revisi as tanggal_upload',
                'revisi.file_review_path as file_path',
                'revisi.nama_file_review_asli as nama_file_asli'
            )
            ->get();

        return $versiItems
            ->concat($reviewItems)
            ->concat($finalEditorItems)
            ->concat($layoutItems)
            ->sortBy([
                ['tanggal_upload', 'asc'],
                ['nomor_versi', 'asc'],
            ])
            ->values();
    }

    private function downloadHistoryFile(Request $request, int $idNaskah): StreamedResponse
    {
        $source = $request->query('source');
        $ref = (int) $request->query('ref');

        abort_unless(in_array($source, ['versi', 'review_attachment', 'final_editor', 'layout'], true), 404);
        abort_unless($ref > 0, 404);

        if ($source === 'versi') {
            $versi = VersiNaskah::query()
                ->where('id_versi', $ref)
                ->where('id_naskah', $idNaskah)
                ->firstOrFail();

            $downloadName = $versi->nama_file_asli ?: basename($versi->file_path);

            return Storage::download($versi->file_path, $downloadName);
        }

        if ($source === 'review_attachment') {
            $revisi = Revisi::query()
                ->where('id_revisi', $ref)
                ->where('id_naskah', $idNaskah)
                ->whereNotNull('file_review_path')
                ->firstOrFail();

            abort_unless(Storage::exists($revisi->file_review_path), 404);

            return Storage::download(
                $revisi->file_review_path,
                $revisi->nama_file_review_asli ?: basename($revisi->file_review_path)
            );
        }

        if ($source === 'final_editor') {
            $naskah = DB::table('naskah')
                ->where('id_naskah', $idNaskah)
                ->where('id_naskah', $ref)
                ->whereNotNull('file_final_editor_path')
                ->firstOrFail();

            abort_unless(Storage::exists($naskah->file_final_editor_path), 404);

            return Storage::download(
                $naskah->file_final_editor_path,
                $naskah->nama_file_final_editor_asli ?: basename($naskah->file_final_editor_path)
            );
        }

        $layout = Layout::query()
            ->where('id_layout', $ref)
            ->where('id_naskah', $idNaskah)
            ->firstOrFail();

        $downloadName = $layout->nama_file_layout_asli ?: basename($layout->file_layout);

        return Storage::download($layout->file_layout, $downloadName);
    }

    private function previewHistoryFile(Request $request, int $idNaskah): BinaryFileResponse
    {
        $source = $request->query('source');
        $ref = (int) $request->query('ref');

        abort_unless(in_array($source, ['review_attachment', 'final_editor', 'layout'], true), 404);
        abort_unless($ref > 0, 404);

        if ($source === 'review_attachment') {
            $revisi = Revisi::query()
                ->where('id_revisi', $ref)
                ->where('id_naskah', $idNaskah)
                ->whereNotNull('file_review_path')
                ->firstOrFail();

            return $this->previewPdfFile($revisi->file_review_path);
        }

        if ($source === 'final_editor') {
            $naskah = DB::table('naskah')
                ->where('id_naskah', $idNaskah)
                ->where('id_naskah', $ref)
                ->whereNotNull('file_final_editor_path')
                ->firstOrFail();

            return $this->previewPdfFile($naskah->file_final_editor_path);
        }

        $layout = Layout::query()
            ->where('id_layout', $ref)
            ->where('id_naskah', $idNaskah)
            ->firstOrFail();

        return $this->previewPdfFile($layout->file_layout);
    }

    private function previewPdfFile(string $path): BinaryFileResponse
    {
        abort_unless(strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf', 404);
        abort_unless(Storage::exists($path), 404);

        return response()->file(Storage::path($path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
