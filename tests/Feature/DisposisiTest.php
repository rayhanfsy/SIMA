<?php

namespace Tests\Feature;

use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisposisiTest extends TestCase
{
    use RefreshDatabase;

    private function suratMasuk(): SuratMasuk
    {
        return SuratMasuk::create([
            'nomor_urut' => '1',
            'nomor_surat' => 'SM/001/2026',
            'tanggal_surat' => '2026-08-14',
            'tanggal_diterima' => '2026-08-14',
            'pengirim' => 'Warga',
            'perihal' => 'Permohonan surat pengantar',
        ]);
    }

    public function test_sekretaris_lurah_bisa_dipilih_sebagai_tujuan(): void
    {
        $lurah = User::factory()->create(['role' => 'lurah']);
        $sm = $this->suratMasuk();

        $this->actingAs($lurah)->post(route('disposisi.store'), [
            'surat_masuk_id' => $sm->id,
            'tujuan' => 'Sekretaris Lurah',
            'sifat' => 'Biasa',
            'isi_disposisi' => 'Mohon ditindaklanjuti.',
        ])->assertRedirect();

        $this->assertDatabaseHas('disposisis', ['tujuan' => 'Sekretaris Lurah']);
    }

    public function test_sifat_rahasia_sudah_tidak_valid(): void
    {
        $lurah = User::factory()->create(['role' => 'lurah']);
        $sm = $this->suratMasuk();

        $this->actingAs($lurah)->post(route('disposisi.store'), [
            'surat_masuk_id' => $sm->id,
            'tujuan' => 'Kasi Pemerintahan',
            'sifat' => 'Rahasia',
            'isi_disposisi' => 'Mohon ditindaklanjuti.',
        ])->assertSessionHasErrors('sifat');
    }
}
