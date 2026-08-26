@extends('components.layout')
@section('title', 'Disposisi | Arsip')

@section('content')
<header class="mb-10 flex justify-between items-end reveal">
    <div>
        <h1 class="font-serif text-4xl font-medium tracking-tight">Disposisi</h1>
    </div>
    @if(auth()->user()->hasRole('lurah', 'admin'))
    <button onclick="document.getElementById('modalDisposisi').showModal()" class="btn-primary text-sm">
        + Buat Disposisi
    </button>
    @endif
</header>

@if(session('success'))
    <div class="mb-6 p-3 rounded-md bg-paleGreen text-inkGreen text-sm border border-[#D7E5D5] reveal">
        {{ session('success') }}
    </div>
@endif

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
    <a href="{{ route('disposisi.export', request()->only('search')) }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm border border-borderline rounded-md hover:bg-canvas transition-colors shrink-0">
        <i class="ph ph-microsoft-excel-logo text-base"></i> Export Excel
    </a>
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
                <td class="px-6 py-4 text-right flex items-center justify-end gap-3">
                    @if($d->status !== 'Selesai' && auth()->user()->hasRole('lurah', 'admin'))
                    <button onclick="document.getElementById('editDisposisi{{ $d->id }}').showModal()" class="text-xs font-medium text-ink hover:underline">Edit</button>
                    @endif
                    @if($d->status !== 'Selesai' && auth()->user()->hasRole('staf', 'admin'))
                    <form action="{{ route('disposisi.selesai', $d->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-xs font-medium text-ink hover:underline">Tandai Selesai</button>
                    </form>
                    @endif
                    @if($d->status === 'Selesai' || (!auth()->user()->hasRole('lurah', 'admin') && !auth()->user()->hasRole('staf', 'admin')))
                    <span class="text-muted text-xs">-</span>
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

<div class="mt-6 reveal">{{ $disposisi->links() }}</div>

{{-- Modal Edit Disposisi (satu per row, ringan karena hanya render untuk yang belum selesai) --}}
@foreach($disposisi as $d)
    @if($d->status !== 'Selesai' && auth()->user()->hasRole('lurah', 'admin'))
    <dialog id="editDisposisi{{ $d->id }}" class="p-0 rounded-xl border border-borderline bg-surface w-full max-w-md shadow-sm">
        <div class="p-8">
            <h3 class="font-serif text-2xl mb-6">Edit Disposisi</h3>
            <form action="{{ route('disposisi.update', $d->id) }}" method="POST" class="flex flex-col gap-4">
                @csrf
                @method('PUT')

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Surat Masuk</label>
                    <input type="text" value="{{ $d->suratMasuk?->nomor_surat ?? 'Surat Dihapus' }} - {{ Str::limit($d->suratMasuk?->perihal, 30) }}" class="input-base bg-canvas text-muted" disabled>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Diteruskan Ke</label>
                        <select name="tujuan" class="input-base" required>
                            @foreach(['Kasi Kesejahteraan Sosial', 'Kasi Ekonomi dan Pembangunan', 'Kasi Pemerintahan', 'Sekretaris Lurah'] as $t)
                            <option value="{{ $t }}" {{ $d->tujuan === $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Sifat</label>
                        <select name="sifat" class="input-base" required>
                            @foreach(['Biasa', 'Penting', 'Segera'] as $s)
                            <option value="{{ $s }}" {{ $d->sifat === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Isi Instruksi / Catatan</label>
                    <textarea name="isi_disposisi" class="input-base min-h-[100px] resize-y" required>{{ $d->isi_disposisi }}</textarea>
                </div>

                <div class="flex justify-end gap-3 mt-5">
                    <button type="button" onclick="document.getElementById('editDisposisi{{ $d->id }}').close()" class="px-4 py-2 text-sm border border-borderline rounded-md hover:bg-canvas transition-colors">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </dialog>
    @endif
@endforeach

@include('surat.modals.tambah_disposisi')
@endsection