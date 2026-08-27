<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SuratKeluar;
use App\Support\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SuratKeluarController extends Controller
{
    public function index(Request $request)
    {
        return view('surat.keluar', [
            'surat' => $this->filteredQuery($request)->paginate(15)->withQueryString(),
        ]);
    }

    public function export(Request $request)
    {
        $rows = $this->filteredQuery($request)->get()->sortBy('nomor_urut')->values()->map(fn ($s, $i) => [
            $i + 1,
            $s->nomor_surat,
            \Carbon\Carbon::parse($s->tanggal_surat)->format('d-m-Y'),
            $s->tujuan,
            $s->perihal,
            $s->keterangan,
            $s->status,
        ]);

        return ExcelExport::download('surat-keluar.xlsx', ['No Urut', 'Nomor Surat', 'Tanggal', 'Tujuan', 'Perihal', 'Keterangan', 'Status'], $rows);
    }

    private function filteredQuery(Request $request)
    {
        $query = SuratKeluar::orderByDesc('tanggal_surat')->orderByDesc('id');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('nomor_urut', 'like', "%{$search}%")
                    ->orWhere('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('tujuan', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function file(Request $request, SuratKeluar $suratKeluar): BinaryFileResponse
    {
        return $this->serveDocument($request, $suratKeluar->file_pdf);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasRole('staf', 'admin'), 403);

        $data = $request->validate($this->rules(), $this->messages());

        if ($request->hasFile('file_pdf')) {
            $data['file_pdf'] = $request->file('file_pdf')->store('arsip/keluar', 'public');
        }

        $surat = SuratKeluar::create($data);
        AuditLog::log('DATA.MUTATION', "Menambahkan Surat Keluar register {$surat->nomor_urut} nomor {$data['nomor_surat']}");

        return back()->with('success', 'Surat keluar berhasil disimpan.');
    }

    public function update(Request $request, SuratKeluar $suratKeluar)
    {
        abort_unless(auth()->user()->hasRole('staf', 'admin'), 403);

        $data = $request->validate($this->rules($suratKeluar->id), $this->messages());

        if ($request->hasFile('file_pdf')) {
            if ($suratKeluar->file_pdf) {
                Storage::disk('public')->delete($suratKeluar->file_pdf);
            }
            $data['file_pdf'] = $request->file('file_pdf')->store('arsip/keluar', 'public');
        }

        $suratKeluar->update($data);
        AuditLog::log('DATA.MUTATION', "Memperbarui Surat Keluar register {$suratKeluar->nomor_urut} nomor {$suratKeluar->nomor_surat}.");

        return back()->with('success', 'Surat keluar berhasil diperbarui.');
    }

    public function destroy(SuratKeluar $suratKeluar)
    {
        abort_unless(auth()->user()->hasRole('staf', 'admin'), 403);

        if ($suratKeluar->file_pdf) {
            Storage::disk('public')->delete($suratKeluar->file_pdf);
        }

        AuditLog::log('DATA.MUTATION', "Menghapus Surat Keluar register {$suratKeluar->nomor_urut} nomor {$suratKeluar->nomor_surat}.");
        $suratKeluar->delete();

        return back()->with('success', 'Surat keluar berhasil dihapus.');
    }

    /**
     * Aturan validasi dipakai bareng oleh store() dan update().
     * $ignoreId diisi saat edit supaya unique check tidak bentrok dengan data itu sendiri.
     */
    private function rules(?int $ignoreId = null): array
    {
        return [
            'nomor_surat' => ['required', 'string', 'max:255', Rule::unique('surat_keluars', 'nomor_surat')->ignore($ignoreId)],
            'tanggal_surat' => 'required|date',
            'tujuan' => 'required|string|max:1000',
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
