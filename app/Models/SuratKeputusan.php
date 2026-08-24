<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKeputusan extends Model
{
    protected $fillable = [
        'nomor_urut',
        'nomor_sk',
        'tanggal_sk',
        'perihal',
        'keterangan',
        'file_pdf',
    ];
}
