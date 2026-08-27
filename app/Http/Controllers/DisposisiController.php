<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Disposisi;
use App\Models\SuratMasuk;
use App\Support\ExcelExport;
use Illuminate\Http\Request;

class DisposisiController extends Controller {
    public function index(Request $request) {
        return view('surat.disposisi', [
            'disposisi' => $this->filteredQuery($request)->paginate(15)->withQueryString(),
            'suratMasuk' => SuratMasuk::whereDoesntHave('disposisis')->latest()->get(),
        ]);
    }

    public function export(Request $request) {
        $rows = $this->filteredQuery($request)->get()->sortBy('id')->values()->map(fn ($d, $i) => [
            $i + 1,
            $d->suratMasuk?->nomor_surat ?? 'Surat Dihapus',
            $d->tujuan,
            $d->sifat,
            $d->isi_disposisi,
            $d->status,
        ]);

        return ExcelExport::download('disposisi.xlsx', ['No Urut', 'Dari Surat', 'Tujuan', 'Sifat', 'Instruksi', 'Status'], $rows);
    }

    private function filteredQuery(Request $request) {
        $query = Disposisi::with('suratMasuk')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tujuan', 'like', "%{$search}%")
                  ->orWhere('isi_disposisi', 'like', "%{$search}%")
                  ->orWhereHas('suratMasuk', function ($sq) use ($search) {
                      $sq->where('nomor_surat', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    public function store(Request $request) {
        abort_unless(auth()->user()->hasRole('lurah', 'admin'), 403);

        $data = $request->validate([
            'surat_masuk_id' => 'required|exists:surat_masuks,id',
            'tujuan' => 'required|string|in:Kasi Kesejahteraan Sosial,Kasi Ekonomi dan Pembangunan,Kasi Pemerintahan,Sekretaris Lurah',
            'sifat' => 'required|string|in:Biasa,Penting,Segera',
            'isi_disposisi' => 'required|string',
        ]);

        $disposisi = Disposisi::create($data);

        AuditLog::log('DATA.MUTATION', "Membuat disposisi ID {$disposisi->id} ke {$disposisi->tujuan}.");

        return back()->with('success', 'Disposisi berhasil dibuat.');
    }

    public function update(Request $request, Disposisi $disposisi) {
        abort_unless(auth()->user()->hasRole('lurah', 'admin'), 403);

        $data = $request->validate([
            'tujuan' => 'required|string|in:Kasi Kesejahteraan Sosial,Kasi Ekonomi dan Pembangunan,Kasi Pemerintahan,Sekretaris Lurah',
            'sifat' => 'required|string|in:Biasa,Penting,Segera',
            'isi_disposisi' => 'required|string',
        ]);

        $disposisi->update($data);

        AuditLog::log('DATA.MUTATION', "Mengubah disposisi ID {$disposisi->id}.");

        return back()->with('success', 'Disposisi berhasil diperbarui.');
    }

    public function selesai(Disposisi $disposisi) {
        abort_unless(auth()->user()->hasRole('staf', 'admin'), 403);

        $disposisi->update(['status' => 'Selesai']);

        AuditLog::log('DATA.MUTATION', "Menyelesaikan disposisi ID {$disposisi->id}.");

        return back()->with('success', 'Disposisi ditandai selesai.');
    }
}
