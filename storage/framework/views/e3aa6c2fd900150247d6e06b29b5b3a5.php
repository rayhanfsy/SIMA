<!-- SIMA/resources/views/auth/login.blade.php -->

<?php $__env->startSection('title', 'Masuk | Arsip'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-[80vh] flex items-center justify-center reveal">
    <div class="bento-card w-full max-w-sm">
        <div class="mb-8 text-center">
            <img src="/images/logo-dc.webp" alt="Logo Kelurahan Dunguscariang" class="h-20 w-20 object-contain mx-auto mb-3">
            <p class="text-sm text-muted mt-2 uppercase">Sistem Informasi Manajemen Arsip</p>
            <p class="text-sm text-muted mt-2 uppercase">Kelurahan Dungus Cariang</p>
        </div>

        <?php if(session('status')): ?>
            <div class="mb-6 p-3 rounded-md bg-canvas border border-borderline text-sm text-center">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>

        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="mb-6 p-3 rounded-md bg-paleRed text-inkRed text-sm text-center border border-[#F5D5D6]">
                <?php echo e($message); ?>

            </div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <form method="POST" action="<?php echo e(route('login.post')); ?>" class="flex flex-col gap-5">
            <?php echo csrf_field(); ?>
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-medium">Email</label>
                <input type="email" name="email" class="input-base" placeholder="nama@gmail.com" required>
            </div>
            
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-medium">Kata Sandi</label>
                <div class="relative">
                    <input type="password" name="password" id="password" class="input-base w-full pr-10" placeholder="Masukkan kata sandi" required>
                    <button type="button" onclick="const p=document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password'; this.firstElementChild.className = p.type === 'password' ? 'ph ph-eye' : 'ph ph-eye-slash'"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-muted">
                        <i class="ph ph-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Local Math Captcha -->
            <div class="flex flex-col gap-1.5 p-4 border border-borderline rounded-md bg-canvas">
                <label class="text-sm font-medium flex items-center gap-2">
                    <i class="ph ph-shield-check text-muted"></i> Verifikasi Keamanan
                </label>
                <div class="flex items-center gap-3 mt-1">
                    <span class="font-mono text-lg font-medium"><?php echo e($num1); ?> + <?php echo e($num2); ?> =</span>
                    <input type="number" name="captcha" class="input-base w-24 text-center" required placeholder="?">
                </div>
                <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="text-xs text-inkRed mt-1"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <button type="submit" class="btn-primary mt-2 w-full">Masuk</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('components.layout', ['isGuest' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/auth/login.blade.php ENDPATH**/ ?>