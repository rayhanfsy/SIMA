<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->after('perihal');
        });

        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->after('perihal');
            $table->string('file_pdf')->nullable()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });

        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->dropColumn(['keterangan', 'file_pdf']);
        });
    }
};
