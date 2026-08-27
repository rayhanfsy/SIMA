<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SuratMasuk extends Model
{
    protected $fillable = [
        'nomor_urut',
        'nomor_surat',
        'tanggal_surat',
        'tanggal_diterima',
        'pengirim',
        'perihal',
        'keterangan',
        'file_pdf',
        'status',
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

    public function disposisis()
    {
        return $this->hasMany(Disposisi::class);
    }
}
