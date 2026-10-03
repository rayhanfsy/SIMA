<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model {
    protected $fillable = [
        'surat_masuk_id', 'tujuan', 'tujuan_tambahan', 'sifat', 'isi_disposisi', 'status', 'tgl_no'
    ];

    public function suratMasuk() {
        return $this->belongsTo(SuratMasuk::class);
    }
}