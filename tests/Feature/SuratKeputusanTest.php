<?php

namespace Tests\Feature;

use App\Models\SuratKeputusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuratKeputusanTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'nomor_urut' => '40',
            'nomor_sk' => 'PD.05.02.01/40/SK/Kel.DC/VIII/2026',
            'tanggal_sk' => '2026-08-14',
            'perihal' => 'SK Ketua RT di RW 009 (periode Nov 2023 - Nov 2028)',
            'keterangan' => 'terbaru',
        ], $overrides);
    }

    public function test_staf_can_register_surat_keputusan(): void
    {
        $staf = User::factory()->create(['role' => 'staf']);

        $this->actingAs($staf)
            ->post(route('surat-keputusan.store'), $this->payload())
            ->assertRedirect();

        $this->assertDatabaseHas('surat_keputusans', ['nomor_sk' => 'PD.05.02.01/40/SK/Kel.DC/VIII/2026']);
    }

    public function test_duplicate_nomor_sk_is_rejected(): void
    {
        $staf = User::factory()->create(['role' => 'staf']);
        SuratKeputusan::create($this->payload());

        $this->actingAs($staf)
            ->post(route('surat-keputusan.store'), $this->payload(['nomor_urut' => '41']))
            ->assertSessionHasErrors('nomor_sk');

        $this->assertSame(1, SuratKeputusan::count());
    }

    public function test_role_without_access_cannot_register_surat_keputusan(): void
    {
        $lurah = User::factory()->create(['role' => 'lurah']);

        $this->actingAs($lurah)
            ->post(route('surat-keputusan.store'), $this->payload())
            ->assertForbidden();
    }
}
