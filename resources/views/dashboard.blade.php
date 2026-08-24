<!-- SIMA/resources/views/dashboard.blade.php -->
@extends('components.layout')
@section('title', 'Dashboard | Arsip')

@section('content')
<header class="mb-12 reveal">
    <h1 class="font-serif text-4xl font-medium tracking-tight">Dashboard</h1>
    <p class="text-muted mt-2">Ringkasan lalu lintas dokumen bulan ini.</p>
</header>

<!-- Bento Grid Stats -->
<main class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12 reveal" style="transition-delay: 100ms;">
    <div class="bento-card flex flex-col gap-3">
        <div class="flex items-center gap-2 text-muted mb-2">
            <i class="ph-fill ph-envelope-simple text-xl"></i>
            <span class="text-xs uppercase tracking-[0.05em] font-medium">Surat Masuk</span>
        </div>
        <span class="text-5xl font-sans tracking-tight">{{ $masuk }}</span>
    </div>
    
    <div class="bento-card flex flex-col gap-3">
        <div class="flex items-center gap-2 text-muted mb-2">
            <i class="ph-fill ph-paper-plane-tilt text-xl"></i>
            <span class="text-xs uppercase tracking-[0.05em] font-medium">Surat Keluar</span>
        </div>
        <span class="text-5xl font-sans tracking-tight">{{ $keluar }}</span>
    </div>
    
    <a href="{{ route('disposisi') }}" class="bento-card flex flex-col gap-3 hover:border-ink transition-colors">
        <div class="flex items-center gap-2 text-muted mb-2">
            <i class="ph-fill ph-files text-xl"></i>
            <span class="text-xs uppercase tracking-[0.05em] font-medium">Perlu Disposisi</span>
        </div>
        <span class="text-5xl font-sans tracking-tight text-inkRed">{{ $perluDisposisi }}</span>
    </a>
</main>

<!-- Aktivitas Terbaru -->
<section class="reveal" style="transition-delay: 200ms;">
    <h2 class="text-sm font-medium uppercase tracking-[0.05em] text-muted mb-4">Masuk Terbaru</h2>
    <div class="bento-card !p-0 overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <tbody class="divide-y divide-borderline">
                @forelse($terbaru as $s)
                <tr class="hover:bg-canvas transition-colors">
                    <td class="px-6 py-4 font-mono text-xs w-48">{{ $s->nomor_surat }}</td>
                    <td class="px-6 py-4 font-medium">{{ $s->pengirim }}</td>
                    <td class="px-6 py-4 text-muted truncate max-w-[300px]">{{ $s->perihal }}</td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-xs text-muted">{{ \Carbon\Carbon::parse($s->tanggal_diterima)->diffForHumans() }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-muted">Belum ada aktivitas dokumen.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection