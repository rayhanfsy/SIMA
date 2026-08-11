<?php $__env->startSection('title', 'Audit Keamanan | SIMA'); ?>

<?php $__env->startSection('content'); ?>
<div class="reveal">
    <div class="mb-7">
        <p class="text-[11px] font-semibold tracking-[0.18em] text-muted uppercase mb-2">Keamanan</p>
        <h1 class="font-sans text-4xl font-semibold tracking-tight">Audit Keamanan</h1>
        <p class="text-muted mt-2 text-sm">Jejak aktivitas penting sistem untuk membantu penelusuran perubahan dan akses.</p>
    </div>

    <section class="bg-surface border border-borderline rounded-xl shadow-sm">
        <form method="GET" action="<?php echo e(route('audit')); ?>" class="p-4 border-b border-borderline">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-3 items-center">
                <div class="relative xl:col-span-4">
                    <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-muted text-lg"></i>
                    <input
                        type="search"
                        name="q"
                        value="<?php echo e(request('q')); ?>"
                        placeholder="Cari pengguna, IP, deskripsi..."
                        class="input-base w-full !pl-10 !py-2.5"
                    >
                </div>

                <div class="xl:col-span-2">
                    <select name="event" class="input-base w-full !py-2.5">
                        <option value="">Semua event</option>
                        <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php if(request('event') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="xl:col-span-2">
                    <input
                        type="date"
                        name="date_from"
                        value="<?php echo e(request('date_from')); ?>"
                        class="input-base w-full !py-2.5"
                        aria-label="Tanggal mulai"
                    >
                </div>

                <div class="xl:col-span-2">
                    <input
                        type="date"
                        name="date_to"
                        value="<?php echo e(request('date_to')); ?>"
                        class="input-base w-full !py-2.5"
                        aria-label="Tanggal akhir"
                    >
                </div>

                <div class="xl:col-span-2 flex gap-2 min-w-0">
                    <button type="submit" class="flex-1 min-w-0 px-3 py-2.5 border border-borderline rounded-md text-sm font-medium hover:bg-canvas transition-colors whitespace-nowrap">
                        Terapkan
                    </button>
                    <?php if(request()->hasAny(['q', 'event', 'date_from', 'date_to'])): ?>
                        <a href="<?php echo e(route('audit')); ?>" class="shrink-0 px-3 py-2.5 text-sm text-muted hover:text-ink transition-colors" title="Reset filter">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php $__errorArgs = ['date_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-2 text-xs text-inkRed">Tanggal akhir tidak boleh lebih awal dari tanggal mulai.</p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </form>

        <div class="overflow-x-auto rounded-b-xl">
            <table class="w-full table-fixed text-left text-sm min-w-[900px]">
                <colgroup>
                    <col class="w-[16%]">
                    <col class="w-[15%]">
                    <col class="w-[18%]">
                    <col class="w-[36%]">
                    <col class="w-[15%]">
                </colgroup>
                <thead>
                    <tr class="border-b border-borderline bg-[#FCFCFB] text-[11px] font-semibold tracking-[0.08em] uppercase text-muted">
                        <th class="px-4 py-3.5">Waktu</th>
                        <th class="px-4 py-3.5">Event</th>
                        <th class="px-4 py-3.5">Pengguna</th>
                        <th class="px-4 py-3.5">Deskripsi</th>
                        <th class="px-4 py-3.5">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-borderline">
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-canvas/70 transition-colors align-top">
                            <td class="px-4 py-3.5 text-[12px] leading-5 text-ink font-medium whitespace-nowrap">
                                <?php echo e($log->created_at->format('d/m/Y, H.i.s')); ?>

                            </td>
                            <td class="px-4 py-3.5">
                                <?php ($event = $log->display_event); ?>
                                <span class="inline-flex max-w-full items-center rounded-full px-2.5 py-1 text-[10px] leading-none font-semibold tracking-[0.04em] bg-paleBlue text-inkBlue whitespace-nowrap">
                                    <?php echo e($event); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-[13px] leading-5 break-words"><?php echo e($log->user->name ?? 'Sistem'); ?></div>
                                <div class="mt-0.5 text-[10px] uppercase tracking-[0.08em] text-muted break-words">
                                    <?php echo e($log->user?->role ?? 'SYSTEM'); ?>

                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-[12px] leading-5 text-[#555451] break-words">
                                <?php echo e($log->description); ?>

                            </td>
                            <td class="px-4 py-3.5 font-mono text-[10px] leading-4 text-[#555451] break-all" title="<?php echo e($log->ip_address ?: '-'); ?>">
                                <?php echo e($log->ip_address ?: '-'); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center text-muted">Belum ada catatan aktivitas yang sesuai filter.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($logs->hasPages()): ?>
            <div class="px-4 py-3.5 border-t border-borderline flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm">
                <span class="text-muted text-xs">
                    Menampilkan <?php echo e($logs->firstItem()); ?>–<?php echo e($logs->lastItem()); ?> dari <?php echo e($logs->total()); ?> log
                </span>
                <div class="flex items-center gap-2">
                    <?php if($logs->onFirstPage()): ?>
                        <span class="px-3 py-1.5 border border-borderline rounded-md text-muted opacity-50">Sebelumnya</span>
                    <?php else: ?>
                        <a href="<?php echo e($logs->previousPageUrl()); ?>" class="px-3 py-1.5 border border-borderline rounded-md hover:bg-canvas">Sebelumnya</a>
                    <?php endif; ?>

                    <span class="px-2 text-muted"><?php echo e($logs->currentPage()); ?> / <?php echo e($logs->lastPage()); ?></span>

                    <?php if($logs->hasMorePages()): ?>
                        <a href="<?php echo e($logs->nextPageUrl()); ?>" class="px-3 py-1.5 border border-borderline rounded-md hover:bg-canvas">Berikutnya</a>
                    <?php else: ?>
                        <span class="px-3 py-1.5 border border-borderline rounded-md text-muted opacity-50">Berikutnya</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('components.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/audit.blade.php ENDPATH**/ ?>