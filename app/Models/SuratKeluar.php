<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    protected $fillable = [
        'nomor_urut',
        'nomor_surat',
        'tanggal_surat',
        'tujuan',
        'perihal',
        'keterangan',
        'file_pdf',
        'status',
    ];
}
