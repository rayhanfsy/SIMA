<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
            'surat' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function file(Request $request, SuratMasuk $suratMasuk): BinaryFileResponse
    {
        return $this->serveDocument($request, $suratMasuk->file_pdf);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasRole('staf', 'admin'), 403);

        $data = $request->validate($this->rules(), $this->messages());

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

    public function update(Request $request, SuratMasuk $suratMasuk)
    {
        abort_unless(auth()->user()->hasRole('staf', 'admin'), 403);

        $data = $request->validate($this->rules($suratMasuk->id), $this->messages());
        $data['tanggal_diterima'] = $data['tanggal_surat'];

        if ($request->hasFile('file_pdf')) {
            if ($suratMasuk->file_pdf) {
                Storage::disk('public')->delete($suratMasuk->file_pdf);
            }
            $data['file_pdf'] = $request->file('file_pdf')->store('arsip/masuk', 'public');
        }

        $suratMasuk->update($data);
        AuditLog::log('DATA.MUTATION', "Memperbarui Surat Masuk register {$suratMasuk->nomor_urut} nomor {$suratMasuk->nomor_surat}.");

        return back()->with('success', 'Surat masuk berhasil diperbarui.');
    }

    public function destroy(SuratMasuk $suratMasuk)
    {
        abort_unless(auth()->user()->hasRole('staf', 'admin'), 403);

        if ($suratMasuk->file_pdf) {
            Storage::disk('public')->delete($suratMasuk->file_pdf);
        }

        AuditLog::log('DATA.MUTATION', "Menghapus Surat Masuk register {$suratMasuk->nomor_urut} nomor {$suratMasuk->nomor_surat}.");
        $suratMasuk->delete();

        return back()->with('success', 'Surat masuk berhasil dihapus.');
    }

    /**
     * Aturan validasi dipakai bareng oleh store() dan update().
     * $ignoreId diisi saat edit supaya unique check tidak bentrok dengan data itu sendiri.
     */
    private function rules(?int $ignoreId = null): array
    {
        return [
            'nomor_urut' => ['required', 'string', 'max:50', Rule::unique('surat_masuks', 'nomor_urut')->ignore($ignoreId)],
            'nomor_surat' => ['required', 'string', 'max:255', Rule::unique('surat_masuks', 'nomor_surat')->ignore($ignoreId)],
            'tanggal_surat' => 'required|date',
            'pengirim' => 'required|string|max:1000',
            'perihal' => 'required|string|max:2000',
            'keterangan' => 'nullable|string|max:2000',
            'file_pdf' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,gif|max:5120',
        ];
    }

    private function messages(): array
    {
        return [
            'nomor_urut.required' => 'Nomor urut register wajib diisi.',
            'nomor_urut.unique' => 'Nomor urut register sudah digunakan.',
            'file_pdf.mimes' => 'Dokumen harus berupa PDF atau gambar JPG, JPEG, PNG, WEBP, atau GIF.',
            'file_pdf.max' => 'Ukuran dokumen maksimal 5 MB.',
        ];
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
