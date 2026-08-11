<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\SuratMasuk;
use Illuminate\Http\Request;

class DisposisiController extends Controller {
    public function index(Request $request) {
        $query = \App\Models\Disposisi::with('suratMasuk')->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('tujuan', 'like', "%{$search}%")
                  ->orWhere('isi_disposisi', 'like', "%{$search}%")
                  ->orWhereHas('suratMasuk', function($q) use ($search) {
                      $q->where('nomor_surat', 'like', "%{$search}%");
                  });
        }

        return view('surat.disposisi', [
            'disposisi' => $query->get(),
            'suratMasuk' => \App\Models\SuratMasuk::latest()->get()
        ]);
    }

    public function store(Request $request) {
        Disposisi::create($request->validate([
            'surat_masuk_id' => 'required|exists:surat_masuks,id',
            'tujuan' => 'required|string',
            'sifat' => 'required|string',
            'isi_disposisi' => 'required|string',
        ]));

        \App\Models\AuditLog::log('DATA.MUTATION', "Menyelesaikan disposisi ID {$disposisi->id}");
        
        return back();
    }
}