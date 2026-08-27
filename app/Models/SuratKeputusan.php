<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->nomor_urut)) {
                $max = static::max(DB::raw('CAST(nomor_urut AS UNSIGNED)'));
                $model->nomor_urut = (string) (($max ? (int) $max : static::count()) + 1);
            }
        });
    }
}
