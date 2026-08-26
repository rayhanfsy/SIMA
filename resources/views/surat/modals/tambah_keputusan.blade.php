<dialog id="modalKeputusan" class="p-0 rounded-xl border border-borderline bg-surface w-[min(820px,94vw)] max-w-none shadow-xl">
    <div class="p-6 sm:p-8 max-h-[92vh] overflow-y-auto">
        <div class="mb-6">
            <h3 id="keputusanModalTitle" class="font-serif text-2xl">Tambah Surat Keputusan</h3>
        </div>

        @if($errors->any())
            <div class="mb-5 p-3 rounded-md bg-paleRed text-inkRed text-sm border border-[#F5D5D6]">
                <ul class="list-disc pl-4 flex flex-col gap-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="formKeputusan" action="{{ route('surat-keputusan.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf
            <input type="hidden" name="_method" id="keputusanMethod" value="{{ old('_method') }}">
            <input type="hidden" name="_id" id="keputusanId" value="{{ old('_id') }}">

            <div class="rounded-lg border border-borderline overflow-hidden">
                <div class="px-4 py-2.5 bg-canvas border-b border-borderline text-xs font-semibold tracking-[0.06em] text-muted uppercase">Surat Keputusan</div>
                <div class="p-4 grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="sm:col-span-2 flex flex-col gap-1.5">
                        <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">No. Urut</label>
                        <input type="text" name="nomor_urut" id="keputusanNomorUrut" class="input-base" placeholder="Contoh: 40" value="{{ old('nomor_urut') }}" required>
                    </div>
                    <div class="sm:col-span-4 flex flex-col gap-1.5">
                        <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Tanggal SK</label>
                        <input type="date" name="tanggal_sk" id="keputusanTanggal" class="input-base" value="{{ old('tanggal_sk') }}" required>
                    </div>
                    <div class="sm:col-span-6 flex flex-col gap-1.5">
                        <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Nomor SK</label>
                        <input type="text" name="nomor_sk" id="keputusanNomorSk" class="input-base" placeholder="Contoh: PD.05.02.01/40/SK/Kel.DC/VIII/2026" value="{{ old('nomor_sk') }}" required>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Perihal</label>
                <textarea name="perihal" id="keputusanPerihal" rows="3" class="input-base resize-y" placeholder="Contoh: SK Ketua RT di RW 001 (periode Okt 2023 - Okt 2028)" required>{{ old('perihal') }}</textarea>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Keterangan</label>
                <textarea name="keterangan" id="keputusanKeterangan" rows="2" class="input-base resize-y" placeholder="Contoh: terbaru">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Dokumen / Foto SK</label>
                <input
                    type="file"
                    name="file_pdf"
                    accept=".pdf,.jpg,.jpeg,.png,.webp,.gif,application/pdf,image/jpeg,image/png,image/webp,image/gif"
                    onchange="previewSelectedRegisterFile(this, 'keputusanFilePreview', 'keputusanFileName')"
                    class="border border-borderline border-dashed rounded-md px-3 py-3 text-sm cursor-pointer file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-canvas file:text-ink"
                >
                <p id="keputusanFileHint" class="text-xs text-muted">PDF, JPG, JPEG, PNG, WEBP, atau GIF. Maksimal 5 MB.</p>
                <div id="keputusanFileName" class="text-xs text-muted"></div>
                <img id="keputusanFilePreview" class="hidden mt-1 max-h-48 max-w-full rounded-md border border-borderline object-contain bg-canvas" alt="Pratinjau gambar yang dipilih">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalKeputusan').close()" class="px-4 py-2 text-sm border border-borderline rounded-md hover:bg-canvas transition-colors">Batal</button>
                <button id="keputusanSubmitBtn" type="submit" class="btn-primary">Simpan Surat Keputusan</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    // Modal ini dipakai ulang untuk tambah maupun edit surat keputusan.
    function openTambahKeputusan() {
        document.getElementById('formKeputusan').reset();
        document.getElementById('formKeputusan').action = "{{ route('surat-keputusan.store') }}";
        document.getElementById('keputusanMethod').value = '';
        document.getElementById('keputusanId').value = '';
        document.getElementById('keputusanModalTitle').textContent = 'Tambah Surat Keputusan';
        document.getElementById('keputusanSubmitBtn').textContent = 'Simpan Surat Keputusan';
        document.getElementById('keputusanFileHint').textContent = 'PDF, JPG, JPEG, PNG, WEBP, atau GIF. Maksimal 5 MB.';
        document.getElementById('keputusanFileName').textContent = '';
        document.getElementById('keputusanFilePreview').classList.add('hidden');
        document.getElementById('modalKeputusan').showModal();
    }

    function openEditKeputusan(id, nomorUrut, tanggal, nomorSk, perihal, keterangan) {
        document.getElementById('formKeputusan').reset();
        document.getElementById('formKeputusan').action = '/surat-keputusan/' + id;
        document.getElementById('keputusanMethod').value = 'PUT';
        document.getElementById('keputusanId').value = id;
        document.getElementById('keputusanModalTitle').textContent = 'Edit Surat Keputusan';
        document.getElementById('keputusanSubmitBtn').textContent = 'Perbarui Surat Keputusan';
        document.getElementById('keputusanNomorUrut').value = nomorUrut;
        document.getElementById('keputusanTanggal').value = tanggal;
        document.getElementById('keputusanNomorSk').value = nomorSk;
        document.getElementById('keputusanPerihal').value = perihal;
        document.getElementById('keputusanKeterangan').value = keterangan;
        document.getElementById('keputusanFileHint').textContent = 'Kosongkan jika tidak ingin mengganti dokumen yang sudah ada.';
        document.getElementById('keputusanFileName').textContent = '';
        document.getElementById('keputusanFilePreview').classList.add('hidden');
        document.getElementById('modalKeputusan').showModal();
    }
</script>

@if($errors->any())
@if(old('_id'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('formKeputusan').action = '/surat-keputusan/' + {{ \Illuminate\Support\Js::from(old('_id')) }};
        document.getElementById('keputusanMethod').value = 'PUT';
        document.getElementById('keputusanModalTitle').textContent = 'Edit Surat Keputusan';
        document.getElementById('keputusanSubmitBtn').textContent = 'Perbarui Surat Keputusan';
        document.getElementById('modalKeputusan').showModal();
    });
</script>
@else
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('modalKeputusan').showModal();
    });
</script>
@endif
@endif

