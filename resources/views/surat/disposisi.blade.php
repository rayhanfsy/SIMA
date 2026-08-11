@extends('components.layout')
@section('title', 'Disposisi | Arsip')

@section('content')
<header class="mb-10 flex justify-between items-end reveal">
    <div>
        <h1 class="font-serif text-4xl font-medium tracking-tight">Disposisi</h1>
    </div>
    @if(auth()->user()->role === 'lurah')
    <button onclick="document.getElementById('modalDisposisi').showModal()" class="btn-primary text-sm">
        + Buat Disposisi
    </button>
    @endif
</header>

<div class="mb-6 flex justify-between items-center reveal" style="transition-delay: 50ms;">
    <form action="{{ route('disposisi') }}" method="GET" class="relative w-full max-w-sm">
        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-muted text-lg"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari disposisi..." class="w-full pl-10 pr-4 py-2 text-sm bg-canvas border border-transparent rounded-md focus:bg-surface focus:border-borderline focus:outline-none transition-colors" autocomplete="off">
        @if(request('search'))
            <a href="{{ route('disposisi') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-inkRed transition-colors">
                <i class="ph ph-x"></i>
            </a>
        @endif
    </form>
</div>

<section class="bento-card !p-0 overflow-x-auto reveal" style="transition-delay: 100ms;">
    <table class="w-full text-left text-sm whitespace-nowrap">
        <thead>
            <tr class="text-muted border-b border-borderline bg-canvas">
                <th class="px-6 py-4 font-normal">Dari Surat</th>
                <th class="px-6 py-4 font-normal">Tujuan</th>
                <th class="px-6 py-4 font-normal">Sifat</th>
                <th class="px-6 py-4 font-normal">Instruksi</th>
                <th class="px-6 py-4 font-normal">Status</th>
                <th class="px-6 py-4 font-normal text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-borderline">
            @forelse($disposisi as $d)
            <tr class="hover:bg-canvas transition-colors">
                <td class="px-6 py-4 font-mono text-xs">{{ $d->suratMasuk?->nomor_surat ?? 'Surat Dihapus' }}</td>
                <td class="px-6 py-4 font-medium">{{ $d->tujuan }}</td>
                <td class="px-6 py-4">
                    <span class="{{ in_array($d->sifat, ['Penting', 'Segera']) ? 'text-inkRed font-medium' : 'text-muted' }}">
                        {{ $d->sifat }}
                    </span>
                </td>
                <td class="px-6 py-4 truncate max-w-[250px]">{{ $d->isi_disposisi }}</td>
                <td class="px-6 py-4">
                    <span class="status-pill {{ $d->status === 'Selesai' ? 'bg-paleGreen text-inkGreen' : 'bg-paleYellow text-inkYellow' }}">
                        {{ $d->status }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    @if($d->status !== 'Selesai' && auth()->user()->role !== 'staf')
                    <form action="{{ route('disposisi.selesai', $d->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-xs font-medium text-ink hover:underline">Tandai Selesai</button>
                    </form>
                    @else
                    <span class="text-muted text-xs italic">Selesai</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-muted">Belum ada data disposisi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</section>

@include('surat.modals.tambah_disposisi')
@endsection