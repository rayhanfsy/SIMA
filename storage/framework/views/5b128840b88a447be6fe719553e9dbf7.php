<dialog id="modalDisposisi" class="p-0 rounded-xl border border-borderline bg-surface w-full max-w-md shadow-sm">
    <div class="p-8">
        <h3 class="font-serif text-2xl mb-6">Buat Disposisi</h3>

        <?php if($errors->any()): ?>
            <div class="mb-5 p-3 rounded-md bg-paleRed text-inkRed text-sm border border-[#F5D5D6]">
                <ul class="list-disc pl-4 flex flex-col gap-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('disposisi.store')); ?>" method="POST" class="flex flex-col gap-4">
            <?php echo csrf_field(); ?>
            
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Pilih Surat Masuk</label>
                <select name="surat_masuk_id" class="input-base" required>
                    <option value="" disabled selected>-- Pilih Surat --</option>
                    <?php $__currentLoopData = $suratMasuk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($sm->id); ?>" <?php echo e(old('surat_masuk_id') == $sm->id ? 'selected' : ''); ?>>
                            <?php echo e($sm->nomor_surat); ?> - <?php echo e(Str::limit($sm->perihal, 30)); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Diteruskan Ke</label>
                    <input type="text" name="tujuan" class="input-base" placeholder="Cth: Kasi Kesra" value="<?php echo e(old('tujuan')); ?>" required>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Sifat</label>
                    <select name="sifat" class="input-base" required>
                        <option value="Biasa" <?php echo e(old('sifat') == 'Biasa' ? 'selected' : ''); ?>>Biasa</option>
                        <option value="Penting" <?php echo e(old('sifat') == 'Penting' ? 'selected' : ''); ?>>Penting</option>
                        <option value="Segera" <?php echo e(old('sifat') == 'Segera' ? 'selected' : ''); ?>>Segera</option>
                        <option value="Rahasia" <?php echo e(old('sifat') == 'Rahasia' ? 'selected' : ''); ?>>Rahasia</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-muted uppercase tracking-[0.05em]">Isi Instruksi / Catatan</label>
                <textarea name="isi_disposisi" class="input-base min-h-[100px] resize-y" placeholder="Cth: Tolong segera diproses..." required><?php echo e(old('isi_disposisi')); ?></textarea>
            </div>

            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="document.getElementById('modalDisposisi').close()" class="px-4 py-2 text-sm border border-borderline rounded-md hover:bg-canvas transition-colors">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</dialog>

<?php if($errors->any()): ?>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById('modalDisposisi').showModal();
    });
</script>
<?php endif; ?><?php /**PATH /var/www/resources/views/surat/modals/tambah_disposisi.blade.php ENDPATH**/ ?>