<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SuratMasuk;
use App\Support\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SuratMasukController extends Controller
{
    public function index(Request $request)
    {
        return view('surat.masuk', [
            'surat' => $this->filteredQuery($request)->paginate(15)->withQueryString(),
        ]);
    }

    public function export(Request $request)
    {
        $rows = $this->filteredQuery($request)->get()->sortBy('nomor_urut')->values()->map(fn ($s, $i) => [
            $i + 1,
            $s->nomor_surat,
            \Carbon\Carbon::parse($s->tanggal_surat)->format('d-m-Y'),
            $s->pengirim,
            $s->perihal,
            $s->keterangan,
        ]);

        return ExcelExport::download('surat-masuk.xlsx', ['No Urut', 'Nomor Surat', 'Tanggal', 'Pengirim', 'Perihal', 'Keterangan'], $rows);
    }

    private function filteredQuery(Request $request)
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

        return $query;
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

        $surat = SuratMasuk::create($data);
        AuditLog::log('DATA.MUTATION', "Menambahkan Surat Masuk register {$surat->nomor_urut} nomor {$data['nomor_surat']}");

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
            'file_pdf.mimes' => 'Dokumen harus berupa PDF atau gambar JPG, JPEG, PNG, WEBP, atau GIF.',
            'file_pdf.max' => 'Ukuran dokumen maksimal 5 MB.',
        ];
    }
}
