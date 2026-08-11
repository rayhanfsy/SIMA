
<?php $__env->startSection('title', 'Disposisi | Arsip'); ?>

<?php $__env->startSection('content'); ?>
<header class="mb-10 flex justify-between items-end reveal">
    <div>
        <h1 class="font-serif text-4xl font-medium tracking-tight">Disposisi</h1>
    </div>
    <?php if(auth()->user()->role === 'lurah'): ?>
    <button onclick="document.getElementById('modalDisposisi').showModal()" class="btn-primary text-sm">
        + Buat Disposisi
    </button>
    <?php endif; ?>
</header>

<div class="mb-6 flex justify-between items-center reveal" style="transition-delay: 50ms;">
    <form action="<?php echo e(route('disposisi')); ?>" method="GET" class="relative w-full max-w-sm">
        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-muted text-lg"></i>
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari disposisi..." class="w-full pl-10 pr-4 py-2 text-sm bg-canvas border border-transparent rounded-md focus:bg-surface focus:border-borderline focus:outline-none transition-colors" autocomplete="off">
        <?php if(request('search')): ?>
            <a href="<?php echo e(route('disposisi')); ?>" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-inkRed transition-colors">
                <i class="ph ph-x"></i>
            </a>
        <?php endif; ?>
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
            <?php $__empty_1 = true; $__currentLoopData = $disposisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-canvas transition-colors">
                <td class="px-6 py-4 font-mono text-xs"><?php echo e($d->suratMasuk?->nomor_surat ?? 'Surat Dihapus'); ?></td>
                <td class="px-6 py-4 font-medium"><?php echo e($d->tujuan); ?></td>
                <td class="px-6 py-4">
                    <span class="<?php echo e(in_array($d->sifat, ['Penting', 'Segera']) ? 'text-inkRed font-medium' : 'text-muted'); ?>">
                        <?php echo e($d->sifat); ?>

                    </span>
                </td>
                <td class="px-6 py-4 truncate max-w-[250px]"><?php echo e($d->isi_disposisi); ?></td>
                <td class="px-6 py-4">
                    <span class="status-pill <?php echo e($d->status === 'Selesai' ? 'bg-paleGreen text-inkGreen' : 'bg-paleYellow text-inkYellow'); ?>">
                        <?php echo e($d->status); ?>

                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <?php if($d->status !== 'Selesai' && auth()->user()->role !== 'staf'): ?>
                    <form action="<?php echo e(route('disposisi.selesai', $d->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="text-xs font-medium text-ink hover:underline">Tandai Selesai</button>
                    </form>
                    <?php else: ?>
                    <span class="text-muted text-xs italic">Selesai</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-muted">Belum ada data disposisi.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php echo $__env->make('surat.modals.tambah_disposisi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('components.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/surat/disposisi.blade.php ENDPATH**/ ?>