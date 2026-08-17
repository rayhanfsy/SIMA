@extends('components.layout')
@section('title', 'Manajemen Akun | SIMA')

@section('content')
<header class="mb-8 flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-end reveal">
    <div>
        <h1 class="font-serif text-4xl font-medium tracking-tight">Manajemen Akun</h1>
        <p class="text-sm text-muted mt-2">Kelola akun staf pelayanan, lurah, dan admin.</p>
    </div>
    <button onclick="document.getElementById('modalTambahAkun').showModal()" class="btn-primary text-sm self-start sm:self-auto">
        + Tambah Akun
    </button>
</header>

@if(session('success'))
    <div class="mb-6 p-3 rounded-md bg-paleGreen text-inkGreen text-sm border border-[#D7E5D5] reveal">
        {{ session('success') }}
    </div>
@endif

<section class="bento-card !p-0 overflow-hidden reveal" style="transition-delay: 100ms;">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-canvas text-muted">
                <tr class="border-b border-borderline">
                    <th class="px-6 py-4 font-medium">Nama</th>
                    <th class="px-6 py-4 font-medium">Email</th>
                    <th class="px-6 py-4 font-medium">Role</th>
                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-borderline bg-surface">
                @forelse($akun as $u)
                    <tr class="hover:bg-canvas/60 transition-colors">
                        <td class="px-6 py-4 font-medium">{{ $u->name }}</td>
                        <td class="px-6 py-4 text-muted">{{ $u->email }}</td>
                        <td class="px-6 py-4">
                            <span class="status-pill bg-canvas text-ink capitalize">{{ $u->role }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-3">
                                <button
                                    type="button"
                                    onclick="openEditAkun('{{ $u->id }}', {{ \Illuminate\Support\Js::from($u->name) }}, {{ \Illuminate\Support\Js::from($u->email) }}, {{ \Illuminate\Support\Js::from($u->role) }})"
                                    class="text-xs font-medium text-ink hover:underline"
                                >Edit</button>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('akun.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-inkRed hover:underline">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-14 text-center text-muted">Belum ada akun.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<div class="mt-6 reveal">{{ $akun->links() }}</div>

@include('akun_modals')
@endsection
