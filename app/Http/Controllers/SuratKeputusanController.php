<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SuratKeputusan;
use App\Support\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SuratKeputusanController extends Controller
{
    public function index(Request $request)
    {
        return view('surat.keputusan', [
            'surat' => $this->filteredQuery($request)->paginate(15)->withQueryString(),
        ]);
    }

    public function export(Request $request)
    {
        $rows = $this->filteredQuery($request)->get()->map(fn ($s) => [
            $s->nomor_urut,
            $s->nomor_sk,
            \Carbon\Carbon::parse($s->tanggal_sk)->format('d-m-Y'),
            $s->perihal,
            $s->keterangan,
        ]);

        return ExcelExport::download('surat-keputusan.xlsx', ['No Urut', 'Nomor SK', 'Tanggal SK', 'Perihal', 'Keterangan'], $rows);
    }

    private function filteredQuery(Request $request)
    {
        $query = SuratKeputusan::orderByDesc('tanggal_sk')->orderByDesc('id');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('nomor_urut', 'like', "%{$search}%")
                    ->orWhere('nomor_sk', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function file(Request $request, SuratKeputusan $suratKeputusan): BinaryFileResponse
    {
        return $this->serveDocument($request, $suratKeputusan->file_pdf);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasRole('staf', 'admin'), 403);

        $data = $request->validate($this->rules(), $this->messages());

        if ($request->hasFile('file_pdf')) {
            $data['file_pdf'] = $request->file('file_pdf')->store('arsip/keputusan', 'public');
        }

        $surat = SuratKeputusan::create($data);
        AuditLog::log('DATA.MUTATION', "Menambahkan Surat Keputusan register {$surat->nomor_urut} nomor {$data['nomor_sk']}");

        return back()->with('success', 'Surat keputusan berhasil disimpan.');
    }

    public function update(Request $request, SuratKeputusan $suratKeputusan)
    {
        abort_unless(auth()->user()->hasRole('staf', 'admin'), 403);

        $data = $request->validate($this->rules($suratKeputusan->id), $this->messages());

        if ($request->hasFile('file_pdf')) {
            if ($suratKeputusan->file_pdf) {
                Storage::disk('public')->delete($suratKeputusan->file_pdf);
            }
            $data['file_pdf'] = $request->file('file_pdf')->store('arsip/keputusan', 'public');
        }

        $suratKeputusan->update($data);
        AuditLog::log('DATA.MUTATION', "Memperbarui Surat Keputusan register {$suratKeputusan->nomor_urut} nomor {$suratKeputusan->nomor_sk}.");

        return back()->with('success', 'Surat keputusan berhasil diperbarui.');
    }

    public function destroy(SuratKeputusan $suratKeputusan)
    {
        abort_unless(auth()->user()->hasRole('staf', 'admin'), 403);

        if ($suratKeputusan->file_pdf) {
            Storage::disk('public')->delete($suratKeputusan->file_pdf);
        }

        AuditLog::log('DATA.MUTATION', "Menghapus Surat Keputusan register {$suratKeputusan->nomor_urut} nomor {$suratKeputusan->nomor_sk}.");
        $suratKeputusan->delete();

        return back()->with('success', 'Surat keputusan berhasil dihapus.');
    }

    /**
     * Aturan validasi dipakai bareng oleh store() dan update().
     * $ignoreId diisi saat edit supaya unique check tidak bentrok dengan data itu sendiri.
     */
    private function rules(?int $ignoreId = null): array
    {
        return [
            'nomor_sk' => ['required', 'string', 'max:255', Rule::unique('surat_keputusans', 'nomor_sk')->ignore($ignoreId)],
            'tanggal_sk' => 'required|date',
            'perihal' => 'required|string|max:2000',
            'keterangan' => 'nullable|string|max:2000',
            'file_pdf' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,gif|max:5120',
        ];
    }

    private function messages(): array
    {
        return [
            'nomor_sk.unique' => 'Nomor SK sudah terdaftar.',
            'file_pdf.mimes' => 'Dokumen harus berupa PDF atau gambar JPG, JPEG, PNG, WEBP, atau GIF.',
            'file_pdf.max' => 'Ukuran dokumen maksimal 5 MB.',
        ];
    }
}
