<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SuratMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratMasuk::orderByDesc('tanggal_surat')->orderByDesc('id');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('nomor_urut', 'like', "%{$search}%")
                    ->orWhere('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('pengirim', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        return view('surat.masuk', [
            'surat' => $query->get(),
        ]);
    }

    public function file(Request $request, SuratMasuk $suratMasuk): BinaryFileResponse
    {
        return $this->serveDocument($request, $suratMasuk->file_pdf);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_urut' => 'required|string|max:50|unique:surat_masuks,nomor_urut',
            'nomor_surat' => 'required|string|max:255|unique:surat_masuks,nomor_surat',
            'tanggal_surat' => 'required|date',
            'pengirim' => 'required|string|max:1000',
            'perihal' => 'required|string|max:2000',
            'keterangan' => 'nullable|string|max:2000',
            'file_pdf' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,gif|max:5120',
        ], [
            'nomor_urut.required' => 'Nomor urut register wajib diisi.',
            'nomor_urut.unique' => 'Nomor urut register sudah digunakan.',
            'file_pdf.mimes' => 'Dokumen harus berupa PDF atau gambar JPG, JPEG, PNG, WEBP, atau GIF.',
            'file_pdf.max' => 'Ukuran dokumen maksimal 5 MB.',
        ]);

        // Struktur database lama memiliki tanggal_diterima. Register fisik hanya
        // memakai satu kolom tanggal, sehingga nilai ini disamakan agar kompatibel.
        $data['tanggal_diterima'] = $data['tanggal_surat'];

        if ($request->hasFile('file_pdf')) {
            $data['file_pdf'] = $request->file('file_pdf')->store('arsip/masuk', 'public');
        }

        SuratMasuk::create($data);
        AuditLog::log('DATA.MUTATION', "Menambahkan Surat Masuk register {$data['nomor_urut']} nomor {$data['nomor_surat']}");

        return back()->with('success', 'Surat masuk berhasil disimpan.');
    }

    private function serveDocument(Request $request, ?string $storedPath): BinaryFileResponse
    {
        abort_unless($storedPath, 404, 'Dokumen tidak ditemukan.');

        $path = $this->normalizePublicPath($storedPath);
        abort_unless(Storage::disk('public')->exists($path), 404, 'File dokumen tidak ditemukan di penyimpanan.');

        $absolutePath = Storage::disk('public')->path($path);
        $mime = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';
        $fileName = basename($path);

        if ($request->boolean('download')) {
            return response()->download($absolutePath, $fileName, [
                'Content-Type' => $mime,
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.addslashes($fileName).'"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function normalizePublicPath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));
        $path = preg_replace('#^https?://[^/]+/storage/#i', '', $path) ?? $path;
        $path = preg_replace('#^/?storage/#i', '', $path) ?? $path;

        return ltrim($path, '/');
    }
}
