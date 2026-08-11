<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->string('nomor_urut', 50)->nullable()->after('id');
        });

        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->string('nomor_urut', 50)->nullable()->after('id');
        });

        // Data lama tetap tampil. ID lama dipakai sebagai nomor urut awal dan
        // dapat dilanjutkan dengan nomor register yang diinput pada data baru.
        DB::table('surat_masuks')->orderBy('id')->get(['id'])->each(function ($row) {
            DB::table('surat_masuks')->where('id', $row->id)->update([
                'nomor_urut' => (string) $row->id,
            ]);
        });

        DB::table('surat_keluars')->orderBy('id')->get(['id'])->each(function ($row) {
            DB::table('surat_keluars')->where('id', $row->id)->update([
                'nomor_urut' => (string) $row->id,
            ]);
        });

        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->unique('nomor_urut', 'surat_masuks_nomor_urut_unique');
        });

        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->unique('nomor_urut', 'surat_keluars_nomor_urut_unique');
        });
    }

    public function down(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->dropUnique('surat_masuks_nomor_urut_unique');
            $table->dropColumn('nomor_urut');
        });

        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->dropUnique('surat_keluars_nomor_urut_unique');
            $table->dropColumn('nomor_urut');
        });
    }
};
