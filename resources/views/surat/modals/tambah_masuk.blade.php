<dialog id="modalTambah" class="p-0 rounded-xl border border-borderline bg-surface w-[min(820px,94vw)] max-w-none shadow-xl">
    <div class="p-6 sm:p-8 max-h-[92vh] overflow-y-auto">
        <div class="mb-6">
            <h3 id="masukModalTitle" class="font-serif text-2xl">Tambah Surat Masuk</h3>
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

        <form id="formMasuk" action="{{ route('surat-masuk.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf
            <input type="hidden" name="_method" id="masukMethod" value="{{ old('_method') }}">
            <input type="hidden" name="_id" id="masukId" value="{{ old('_id') }}">

            <div class="rounded-lg border border-borderline overflow-hidden">
                <div class="px-4 py-2.5 bg-canvas border-b border-borderline text-xs font-semibold tracking-[0.06em] text-muted uppercase">Surat Masuk</div>
                <div class="p-4 grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="sm:col-span-2 flex flex-col gap-1.5">
                        <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">No. Urut</label>
                        <input type="text" name="nomor_urut" id="masukNomorUrut" class="input-base" placeholder="Contoh: 38" value="{{ old('nomor_urut') }}" required>
                    </div>
                    <div class="sm:col-span-4 flex flex-col gap-1.5">
                        <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Tanggal</label>
                        <input type="date" name="tanggal_surat" id="masukTanggal" class="input-base" value="{{ old('tanggal_surat') }}" required>
                    </div>
                    <div class="sm:col-span-6 flex flex-col gap-1.5">
                        <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Nomor Surat</label>
                        <input type="text" name="nomor_surat" id="masukNomorSurat" class="input-base" placeholder="Nomor surat masuk" value="{{ old('nomor_surat') }}" required>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Nama, Pekerjaan dan Alamat Pengirim</label>
                <textarea name="pengirim" id="masukPengirim" rows="3" class="input-base resize-y" placeholder="Instansi, alamat/identitas pengirim" required>{{ old('pengirim') }}</textarea>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Perihal / Uraian Pokok Masalah</label>
                <textarea name="perihal" id="masukPerihal" rows="3" class="input-base resize-y" placeholder="Tuliskan perihal atau uraian pokok masalah" required>{{ old('perihal') }}</textarea>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Keterangan</label>
                <textarea name="keterangan" id="masukKeterangan" rows="2" class="input-base resize-y" placeholder="Contoh: jadwal kegiatan, tindak lanjut, atau catatan lain">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Dokumen / Foto Surat</label>
                <input
                    type="file"
                    name="file_pdf"
                    accept=".pdf,.jpg,.jpeg,.png,.webp,.gif,application/pdf,image/jpeg,image/png,image/webp,image/gif"
                    onchange="previewSelectedRegisterFile(this, 'masukFilePreview', 'masukFileName')"
                    class="border border-borderline border-dashed rounded-md px-3 py-3 text-sm cursor-pointer file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-canvas file:text-ink"
                >
                <p id="masukFileHint" class="text-xs text-muted">PDF, JPG, JPEG, PNG, WEBP, atau GIF. Maksimal 5 MB.</p>
                <div id="masukFileName" class="text-xs text-muted"></div>
                <img id="masukFilePreview" class="hidden mt-1 max-h-48 max-w-full rounded-md border border-borderline object-contain bg-canvas" alt="Pratinjau gambar yang dipilih">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalTambah').close()" class="px-4 py-2 text-sm border border-borderline rounded-md hover:bg-canvas transition-colors">Batal</button>
                <button id="masukSubmitBtn" type="submit" class="btn-primary">Simpan Surat Masuk</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    // Modal ini dipakai ulang untuk tambah maupun edit surat masuk.
    function openTambahMasuk() {
        document.getElementById('formMasuk').reset();
        document.getElementById('formMasuk').action = "{{ route('surat-masuk.store') }}";
        document.getElementById('masukMethod').value = '';
        document.getElementById('masukId').value = '';
        document.getElementById('masukModalTitle').textContent = 'Tambah Surat Masuk';
        document.getElementById('masukSubmitBtn').textContent = 'Simpan Surat Masuk';
        document.getElementById('masukFileHint').textContent = 'PDF, JPG, JPEG, PNG, WEBP, atau GIF. Maksimal 5 MB.';
        document.getElementById('masukFileName').textContent = '';
        document.getElementById('masukFilePreview').classList.add('hidden');
        document.getElementById('modalTambah').showModal();
    }

    function openEditMasuk(id, nomorUrut, tanggal, nomorSurat, pengirim, perihal, keterangan) {
        document.getElementById('formMasuk').reset();
        document.getElementById('formMasuk').action = '/surat-masuk/' + id;
        document.getElementById('masukMethod').value = 'PUT';
        document.getElementById('masukId').value = id;
        document.getElementById('masukModalTitle').textContent = 'Edit Surat Masuk';
        document.getElementById('masukSubmitBtn').textContent = 'Perbarui Surat Masuk';
        document.getElementById('masukNomorUrut').value = nomorUrut;
        document.getElementById('masukTanggal').value = tanggal;
        document.getElementById('masukNomorSurat').value = nomorSurat;
        document.getElementById('masukPengirim').value = pengirim;
        document.getElementById('masukPerihal').value = perihal;
        document.getElementById('masukKeterangan').value = keterangan;
        document.getElementById('masukFileHint').textContent = 'Kosongkan jika tidak ingin mengganti dokumen yang sudah ada.';
        document.getElementById('masukFileName').textContent = '';
        document.getElementById('masukFilePreview').classList.add('hidden');
        document.getElementById('modalTambah').showModal();
    }
</script>

@if($errors->any())
@if(old('_id'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('formMasuk').action = '/surat-masuk/' + {{ \Illuminate\Support\Js::from(old('_id')) }};
        document.getElementById('masukMethod').value = 'PUT';
        document.getElementById('masukModalTitle').textContent = 'Edit Surat Masuk';
        document.getElementById('masukSubmitBtn').textContent = 'Perbarui Surat Masuk';
        document.getElementById('modalTambah').showModal();
    });
</script>
@else
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('modalTambah').showModal();
    });
</script>
@endif
@endif

