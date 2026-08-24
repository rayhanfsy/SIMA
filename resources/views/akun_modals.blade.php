<!-- Modal Tambah Akun -->
<dialog id="modalTambahAkun" class="p-0 rounded-xl border border-borderline bg-surface w-full max-w-md shadow-sm">
    <div class="p-8">
        <h3 class="font-serif text-2xl mb-6">Tambah Akun</h3>

        @if($errors->any())
            <div class="mb-5 p-3 rounded-md bg-paleRed text-inkRed text-sm border border-[#F5D5D6]">
                <ul class="list-disc pl-4 flex flex-col gap-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('akun.store') }}" method="POST" class="flex flex-col gap-4">
            @csrf
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Nama</label>
                <input type="text" name="name" class="input-base" value="{{ old('name') }}" required>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Email</label>
                <input type="email" name="email" class="input-base" value="{{ old('email') }}" required>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Kata Sandi</label>
                <div class="relative">
                    <input type="password" name="password" class="input-base w-full pr-10" minlength="8" required>
                    <button type="button" onclick="togglePasswordVisibility(this)" tabindex="-1" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted hover:text-ink">
                        <i class="ph ph-eye text-base"></i>
                    </button>
                </div>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Role</label>
                <select name="role" class="input-base" required>
                    <option value="" disabled selected>-- Pilih Role --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r }}" class="capitalize" {{ old('role') == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="document.getElementById('modalTambahAkun').close()" class="px-4 py-2 text-sm border border-borderline rounded-md hover:bg-canvas transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</dialog>

<!-- Modal Edit Akun (satu modal dipakai ulang untuk semua baris) -->
<dialog id="modalEditAkun" class="p-0 rounded-xl border border-borderline bg-surface w-full max-w-md shadow-sm">
    <div class="p-8">
        <h3 class="font-serif text-2xl mb-6">Edit Akun</h3>

        <form id="formEditAkun" method="POST" class="flex flex-col gap-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_akun_id" id="editAkunId">

            @if($errors->any() && old('_akun_id'))
                <div class="mb-1 p-3 rounded-md bg-paleRed text-inkRed text-sm border border-[#F5D5D6]">
                    <ul class="list-disc pl-4 flex flex-col gap-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Nama</label>
                <input type="text" name="name" id="editAkunName" class="input-base" required>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Email</label>
                <input type="email" name="email" id="editAkunEmail" class="input-base" required>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Kata Sandi Baru</label>
                <div class="relative">
                    <input type="password" name="password" class="input-base w-full pr-10" minlength="8" placeholder="Kosongkan jika tidak diubah">
                    <button type="button" onclick="togglePasswordVisibility(this)" tabindex="-1" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted hover:text-ink">
                        <i class="ph ph-eye text-base"></i>
                    </button>
                </div>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Role</label>
                <select name="role" id="editAkunRole" class="input-base" required>
                    @foreach($roles as $r)
                        <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="document.getElementById('modalEditAkun').close()" class="px-4 py-2 text-sm border border-borderline rounded-md hover:bg-canvas transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    function openEditAkun(id, name, email, role) {
        document.getElementById('formEditAkun').action = '/akun/' + id;
        document.getElementById('editAkunId').value = id;
        document.getElementById('editAkunName').value = name;
        document.getElementById('editAkunEmail').value = email;
        document.getElementById('editAkunRole').value = role;
        document.getElementById('modalEditAkun').showModal();
    }
</script>

@if($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", () => {
        @if(old('_akun_id'))
            openEditAkun({{ \Illuminate\Support\Js::from(old('_akun_id')) }}, {{ \Illuminate\Support\Js::from(old('name')) }}, {{ \Illuminate\Support\Js::from(old('email')) }}, {{ \Illuminate\Support\Js::from(old('role')) }});
        @else
            document.getElementById('modalTambahAkun').showModal();
        @endif
    });
</script>
@endif
