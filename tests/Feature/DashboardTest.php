<?php

namespace Tests\Feature;

use App\Models\Disposisi;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_perlu_disposisi_menghitung_surat_masuk_yang_belum_didisposisi(): void
    {
        $user = User::factory()->create(['role' => 'staf']);

        $sudahDisposisi = SuratMasuk::create([
            'nomor_urut' => '1',
            'nomor_surat' => 'SM/001/2026',
            'tanggal_surat' => '2026-08-14',
            'tanggal_diterima' => '2026-08-14',
            'pengirim' => 'Warga A',
            'perihal' => 'Perihal A',
        ]);
        Disposisi::create([
            'surat_masuk_id' => $sudahDisposisi->id,
            'tujuan' => 'Kasi Pemerintahan',
            'sifat' => 'Biasa',
            'isi_disposisi' => 'Sudah diproses.',
        ]);

        SuratMasuk::create([
            'nomor_urut' => '2',
            'nomor_surat' => 'SM/002/2026',
            'tanggal_surat' => '2026-08-15',
            'tanggal_diterima' => '2026-08-15',
            'pengirim' => 'Warga B',
            'perihal' => 'Perihal B',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('perluDisposisi', 1);
    }
}
