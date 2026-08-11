<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('disposisis', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel surat_masuks
            $table->foreignId('surat_masuk_id')->constrained()->cascadeOnDelete();
            $table->string('tujuan');
            $table->string('sifat')->default('Biasa'); // Biasa, Penting, Segera
            $table->text('isi_disposisi');
            $table->string('status')->default('Menunggu'); // Menunggu, Proses, Selesai
            $table->timestamps();
        });
    }
};