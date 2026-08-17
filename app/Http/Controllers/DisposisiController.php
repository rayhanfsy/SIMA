<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Disposisi;
use App\Models\SuratMasuk;
use Illuminate\Http\Request;

class DisposisiController extends Controller {
    public function index(Request $request) {
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

        return view('surat.disposisi', [
            'disposisi' => $query->paginate(15)->withQueryString(),
            'suratMasuk' => SuratMasuk::latest()->get(),
        ]);
    }

    public function store(Request $request) {
        abort_unless(auth()->user()->hasRole('lurah', 'admin'), 403);

        $data = $request->validate([
            'surat_masuk_id' => 'required|exists:surat_masuks,id',
            'tujuan' => 'required|string|in:Kasi Kesejahteraan Sosial,Kasi Ekonomi dan Pembangunan,Kasi Pemerintahan',
            'sifat' => 'required|string',
            'isi_disposisi' => 'required|string',
        ]);

        $disposisi = Disposisi::create($data);

        AuditLog::log('DATA.MUTATION', "Membuat disposisi ID {$disposisi->id} ke {$disposisi->tujuan}.");

        return back()->with('success', 'Disposisi berhasil dibuat.');
    }

    public function selesai(Disposisi $disposisi) {
        abort_unless(auth()->user()->hasRole('staf', 'admin'), 403);

        $disposisi->update(['status' => 'Selesai']);

        AuditLog::log('DATA.MUTATION', "Menyelesaikan disposisi ID {$disposisi->id}.");

        return back()->with('success', 'Disposisi ditandai selesai.');
    }
}
