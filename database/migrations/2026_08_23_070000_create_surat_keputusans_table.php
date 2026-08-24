<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_keputusans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_urut', 50)->unique();
            $table->string('nomor_sk')->unique();
            $table->date('tanggal_sk');
            $table->text('perihal');
            $table->text('keterangan')->nullable();
            $table->string('file_pdf')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_keputusans');
    }
};
