<dialog id="modalKeluar" class="p-0 rounded-xl border border-borderline bg-surface w-[min(820px,94vw)] max-w-none shadow-xl">
    <div class="p-6 sm:p-8 max-h-[92vh] overflow-y-auto">
        <div class="mb-6">
            <h3 id="keluarModalTitle" class="font-serif text-2xl">Tambah Surat Keluar</h3>
            <p class="text-sm text-muted mt-1">Isian mengikuti kolom buku register surat keluar yang Anda kirim.</p>
        </div>

        <?php if($errors->any()): ?>
            <div class="mb-5 p-3 rounded-md bg-paleRed text-inkRed text-sm border border-[#F5D5D6]">
                <ul class="list-disc pl-4 flex flex-col gap-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="formKeluar" action="<?php echo e(route('surat-keluar.store')); ?>" method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_method" id="keluarMethod" value="<?php echo e(old('_method')); ?>">
            <input type="hidden" name="_id" id="keluarId" value="<?php echo e(old('_id')); ?>">

            <div class="rounded-lg border border-borderline overflow-hidden">
                <div class="px-4 py-2.5 bg-canvas border-b border-borderline text-xs font-semibold tracking-[0.06em] text-muted uppercase">Surat Keluar</div>
                <div class="p-4 grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="sm:col-span-2 flex flex-col gap-1.5">
                        <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">No. Urut</label>
                        <input type="text" name="nomor_urut" id="keluarNomorUrut" class="input-base" placeholder="Contoh: 155" value="<?php echo e(old('nomor_urut')); ?>" required>
                    </div>
                    <div class="sm:col-span-4 flex flex-col gap-1.5">
                        <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Tanggal</label>
                        <input type="date" name="tanggal_surat" id="keluarTanggal" class="input-base" value="<?php echo e(old('tanggal_surat')); ?>" required>
                    </div>
                    <div class="sm:col-span-6 flex flex-col gap-1.5">
                        <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Nomor Surat</label>
                        <input type="text" name="nomor_surat" id="keluarNomorSurat" class="input-base" placeholder="Nomor surat keluar" value="<?php echo e(old('nomor_surat')); ?>" required>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Dikirim Kepada</label>
                <textarea name="tujuan" id="keluarTujuan" rows="3" class="input-base resize-y" placeholder="Nama instansi, pihak, atau alamat tujuan" required><?php echo e(old('tujuan')); ?></textarea>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Perihal / Uraian Pokok Masalah</label>
                <textarea name="perihal" id="keluarPerihal" rows="3" class="input-base resize-y" placeholder="Tuliskan perihal atau uraian pokok masalah" required><?php echo e(old('perihal')); ?></textarea>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Keterangan</label>
                <textarea name="keterangan" id="keluarKeterangan" rows="2" class="input-base resize-y" placeholder="Contoh: periode, petugas, tindak lanjut, atau catatan lain"><?php echo e(old('keterangan')); ?></textarea>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Dokumen / Foto Surat</label>
                <input
                    type="file"
                    name="file_pdf"
                    accept=".pdf,.jpg,.jpeg,.png,.webp,.gif,application/pdf,image/jpeg,image/png,image/webp,image/gif"
                    onchange="previewSelectedRegisterFile(this, 'keluarFilePreview', 'keluarFileName')"
                    class="border border-borderline border-dashed rounded-md px-3 py-3 text-sm cursor-pointer file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-canvas file:text-ink"
                >
                <p id="keluarFileHint" class="text-xs text-muted">PDF, JPG, JPEG, PNG, WEBP, atau GIF. Maksimal 5 MB.</p>
                <div id="keluarFileName" class="text-xs text-muted"></div>
                <img id="keluarFilePreview" class="hidden mt-1 max-h-48 max-w-full rounded-md border border-borderline object-contain bg-canvas" alt="Pratinjau gambar yang dipilih">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalKeluar').close()" class="px-4 py-2 text-sm border border-borderline rounded-md hover:bg-canvas transition-colors">Batal</button>
                <button id="keluarSubmitBtn" type="submit" class="btn-primary">Simpan Surat Keluar</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    // Modal ini dipakai ulang untuk tambah maupun edit surat keluar.
    function openTambahKeluar() {
        document.getElementById('formKeluar').reset();
        document.getElementById('formKeluar').action = "<?php echo e(route('surat-keluar.store')); ?>";
        document.getElementById('keluarMethod').value = '';
        document.getElementById('keluarId').value = '';
        document.getElementById('keluarModalTitle').textContent = 'Tambah Surat Keluar';
        document.getElementById('keluarSubmitBtn').textContent = 'Simpan Surat Keluar';
        document.getElementById('keluarFileHint').textContent = 'PDF, JPG, JPEG, PNG, WEBP, atau GIF. Maksimal 5 MB.';
        document.getElementById('keluarFileName').textContent = '';
        document.getElementById('keluarFilePreview').classList.add('hidden');
        document.getElementById('modalKeluar').showModal();
    }

    function openEditKeluar(id, nomorUrut, tanggal, nomorSurat, tujuan, perihal, keterangan) {
        document.getElementById('formKeluar').reset();
        document.getElementById('formKeluar').action = '/surat-keluar/' + id;
        document.getElementById('keluarMethod').value = 'PUT';
        document.getElementById('keluarId').value = id;
        document.getElementById('keluarModalTitle').textContent = 'Edit Surat Keluar';
        document.getElementById('keluarSubmitBtn').textContent = 'Perbarui Surat Keluar';
        document.getElementById('keluarNomorUrut').value = nomorUrut;
        document.getElementById('keluarTanggal').value = tanggal;
        document.getElementById('keluarNomorSurat').value = nomorSurat;
        document.getElementById('keluarTujuan').value = tujuan;
        document.getElementById('keluarPerihal').value = perihal;
        document.getElementById('keluarKeterangan').value = keterangan;
        document.getElementById('keluarFileHint').textContent = 'Kosongkan jika tidak ingin mengganti dokumen yang sudah ada.';
        document.getElementById('keluarFileName').textContent = '';
        document.getElementById('keluarFilePreview').classList.add('hidden');
        document.getElementById('modalKeluar').showModal();
    }
</script>

<?php if($errors->any()): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        <?php if(old('_id')): ?>
            document.getElementById('formKeluar').action = '/surat-keluar/' + <?php echo e(\Illuminate\Support\Js::from(old('_id'))); ?>;
            document.getElementById('keluarMethod').value = 'PUT';
            document.getElementById('keluarModalTitle').textContent = 'Edit Surat Keluar';
            document.getElementById('keluarSubmitBtn').textContent = 'Perbarui Surat Keluar';
        <?php endif; ?>
        document.getElementById('modalKeluar').showModal();
    });
</script>
<?php endif; ?>
<?php /**PATH /var/www/resources/views/surat/modals/tambah_keluar.blade.php ENDPATH**/ ?>