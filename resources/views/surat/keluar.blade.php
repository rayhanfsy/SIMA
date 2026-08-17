@extends('components.layout')
@section('title', 'Surat Keluar | SIMA')

@section('content')
<header class="mb-8 flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-end reveal">
    <div>
        <h1 class="font-serif text-4xl font-medium tracking-tight">Surat Keluar</h1>
        <p class="text-sm text-muted mt-2">Register surat keluar sesuai format buku agenda.</p>
    </div>
    @if(auth()->user()->hasRole('staf', 'admin'))
    <button onclick="openTambahKeluar()" class="btn-primary text-sm self-start sm:self-auto">
        + Tambah Surat Keluar
    </button>
    @endif
</header>

@if(session('success'))
    <div class="mb-6 p-3 rounded-md bg-paleGreen text-inkGreen text-sm border border-[#D7E5D5] reveal">
        {{ session('success') }}
    </div>
@endif

<div class="mb-6 flex justify-between items-center reveal" style="transition-delay: 50ms;">
    <form action="{{ route('surat-keluar') }}" method="GET" class="relative w-full max-w-xl">
        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-muted text-lg"></i>
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari no. urut, nomor surat, tujuan, perihal, atau keterangan..."
            class="w-full pl-10 pr-10 py-2.5 text-sm bg-surface border border-borderline rounded-md focus:border-ink focus:outline-none transition-colors"
            autocomplete="off"
        >
        @if(request('search'))
            <a href="{{ route('surat-keluar') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-inkRed transition-colors" title="Hapus pencarian">
                <i class="ph ph-x"></i>
            </a>
        @endif
    </form>
</div>

<section class="bento-card !p-0 overflow-hidden reveal" style="transition-delay: 100ms;">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm min-w-[1320px] register-table">
            <thead class="bg-canvas text-muted">
                <tr class="border-b border-borderline">
                    <th rowspan="2" class="px-4 py-3 font-medium text-center align-middle w-24">NO.<br>URUT</th>
                    <th colspan="2" class="px-4 py-3 font-medium text-center border-l border-borderline">SURAT KELUAR</th>
                    <th rowspan="2" class="px-4 py-3 font-medium text-center align-middle min-w-[240px] border-l border-borderline">DIKIRIM KEPADA</th>
                    <th rowspan="2" class="px-4 py-3 font-medium text-center align-middle min-w-[340px] border-l border-borderline">PERIHAL / URAIAN<br>POKOK MASALAH</th>
                    <th rowspan="2" class="px-4 py-3 font-medium text-center align-middle min-w-[220px] border-l border-borderline">KETERANGAN</th>
                    <th rowspan="2" class="px-4 py-3 font-medium text-center align-middle w-40 border-l border-borderline">DOKUMEN</th>
                    <th rowspan="2" class="px-4 py-3 font-medium text-center align-middle w-24 border-l border-borderline">AKSI</th>
                </tr>
                <tr class="border-b border-borderline">
                    <th class="px-4 py-3 font-medium text-center w-36 border-l border-borderline">TANGGAL</th>
                    <th class="px-4 py-3 font-medium text-center min-w-[230px] border-l border-borderline">NOMOR</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-borderline bg-surface">
                @forelse($surat as $s)
                    @php
                        $extension = $s->file_pdf ? strtolower(pathinfo($s->file_pdf, PATHINFO_EXTENSION)) : null;
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                        $fileUrl = $s->file_pdf ? route('surat-keluar.file', $s) : null;
                    @endphp
                    <tr class="hover:bg-canvas/60 transition-colors align-top">
                        <td class="px-4 py-4 text-center font-mono text-xs">{{ $s->nomor_urut ?: $s->id }}</td>
                        <td class="px-4 py-4 text-center whitespace-nowrap border-l border-borderline">{{ \Carbon\Carbon::parse($s->tanggal_surat)->format('d-m-Y') }}</td>
                        <td class="px-4 py-4 font-mono text-xs break-words border-l border-borderline">{{ $s->nomor_surat }}</td>
                        <td class="px-4 py-4 whitespace-pre-line break-words border-l border-borderline">{{ $s->tujuan }}</td>
                        <td class="px-4 py-4 whitespace-pre-line break-words border-l border-borderline">{{ $s->perihal }}</td>
                        <td class="px-4 py-4 whitespace-pre-line break-words text-muted border-l border-borderline">{{ $s->keterangan ?: '-' }}</td>
                        <td class="px-4 py-4 border-l border-borderline">
                            @if($s->file_pdf)
                                <div class="flex flex-col items-start gap-2">
                                    <button
                                        type="button"
                                        data-url="{{ $fileUrl }}"
                                        data-type="{{ $isImage ? 'image' : 'pdf' }}"
                                        data-title="Surat Keluar {{ $s->nomor_surat }}"
                                        onclick="openDocumentPreview(this.dataset.url, this.dataset.type, this.dataset.title)"
                                        class="inline-flex items-center gap-2 text-ink font-medium hover:underline"
                                    >
                                        <i class="ph-fill {{ $isImage ? 'ph-file-image text-inkBlue' : 'ph-file-pdf text-inkRed' }} text-lg"></i>
                                        Lihat
                                    </button>
                                    <a href="{{ $fileUrl }}?download=1" class="text-xs text-muted hover:text-ink hover:underline">Unduh</a>
                                </div>
                            @else
                                <span class="text-muted text-xs italic">Tidak ada file</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center border-l border-borderline">
                            @if(auth()->user()->hasRole('staf', 'admin'))
                                <div class="flex items-center justify-center gap-3">
                                    <button
                                        type="button"
                                        onclick="openEditKeluar({{ \Illuminate\Support\Js::from($s->id) }}, {{ \Illuminate\Support\Js::from($s->nomor_urut) }}, {{ \Illuminate\Support\Js::from(\Carbon\Carbon::parse($s->tanggal_surat)->format('Y-m-d')) }}, {{ \Illuminate\Support\Js::from($s->nomor_surat) }}, {{ \Illuminate\Support\Js::from($s->tujuan) }}, {{ \Illuminate\Support\Js::from($s->perihal) }}, {{ \Illuminate\Support\Js::from($s->keterangan) }})"
                                        class="text-ink hover:underline text-xs font-medium"
                                    >Edit</button>
                                    <form action="{{ route('surat-keluar.destroy', $s) }}" method="POST" onsubmit="return confirm('Hapus surat keluar register {{ $s->nomor_urut }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-inkRed hover:underline text-xs font-medium">Hapus</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-14 text-center text-muted">Belum ada data surat keluar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<div class="mt-6 reveal">{{ $surat->links() }}</div>

@include('surat.modals.tambah_keluar')
@include('surat.modals.preview_dokumen')
@endsection
