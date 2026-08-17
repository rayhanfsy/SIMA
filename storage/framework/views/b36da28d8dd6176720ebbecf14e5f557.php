<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Arsip Kelurahan'); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500&family=Geist+Mono&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        canvas: '#F7F6F3', surface: '#FFFFFF', borderline: '#EAEAEA',
                        ink: '#111111', muted: '#787774', 
                        paleBlue: '#E1F3FE', inkBlue: '#1F6C9F',
                        paleGreen: '#EDF3EC', inkGreen: '#346538',
                        paleRed: '#FDEBEC', inkRed: '#9F2F2D',
                        paleYellow: '#FBF3DB', inkYellow: '#956400'
                    },
                    fontFamily: {
                        sans: ['Geist', 'sans-serif'],
                        serif: ['Newsreader', 'serif'],
                        mono: ['Geist Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        body { 
            background-color: theme('colors.canvas'); 
            color: theme('colors.ink'); 
        }
        .bento-card { 
            background: theme('colors.surface'); 
            border: 1px solid theme('colors.borderline'); 
            border-radius: 8px; padding: 24px; 
        }
        .btn-primary { 
            background: theme('colors.ink'); 
            color: #FFF; padding: 8px 16px; 
            border-radius: 6px; 
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1); 
        }
        .btn-primary:active { 
            transform: scale(0.98); 
        }
        .status-pill { 
            padding: 4px 10px; 
            border-radius: 9999px; 
            font-size: 0.75rem; 
            letter-spacing: 0.05em; 
            text-transform: uppercase; 
            font-weight: 500; 
        }
        .input-base { 
            border: 1px solid theme('colors.borderline'); 
            border-radius: 6px; 
            padding: 8px 12px; 
            font-size: 0.875rem; 
            outline: none; 
            transition: border-color 0.2s; 
            background: theme('colors.surface'); 
        }
        .input-base:focus { 
            border-color: theme('colors.ink'); 
        }
        
        dialog::backdrop { 
            background: rgba(17, 17, 17, 0.4); 
            backdrop-filter: blur(2px); 
        }
        dialog[open] { 
            animation: fade-in 0.2s ease-out; 
        }
        @keyframes fade-in { 
            from { opacity: 0; transform: translateY(12px); 
            } to { 
                opacity: 1; 
                transform: translateY(0); 
            } 
        }
        .reveal { 
            opacity: 0; 
            transform: translateY(16px); 
            transition: opacity 0.6s ease-out, transform 0.6s ease-out; 
        }
        .reveal.active { 
            opacity: 1; 
            transform: translateY(0); 
        }
    </style>
</head>
<body class="antialiased flex h-screen overflow-hidden">

    <?php if(auth()->guard()->check()): ?>
    <!-- Sidebar -->
    <aside class="w-64 bg-surface border-r border-borderline flex flex-col justify-between hidden md:flex shrink-0">
        <div>
            <!-- Header Sidebar: Logo & Nama Aplikasi -->
            <div class="h-16 flex items-center gap-3 px-6 border-b border-borderline">
                <img src="/images/logo-dc.webp" alt="Logo Kelurahan Dunguscariang" class="h-12 w-12 object-contain shrink-0">
                <span class="text-xl text-borderline">|</span>
                <div class="flex flex-col justify-center leading-tight">
                    <span class="font-serif text-lg font-medium tracking-tight">SIMA</span>
                    <span class="text-[10px] text-muted tracking-wide uppercase">Dungus Cariang</span>
                </div>
            </div>

            <!-- Profil User (Di Atas) -->
            <div class="flex items-center gap-3 px-6 py-5 border-b border-borderline">
                <div class="w-9 h-9 rounded-full bg-borderline flex items-center justify-center text-xs font-medium shrink-0 uppercase text-ink">
                    <?php echo e(substr(auth()->user()->name, 0, 2)); ?>

                </div>
                <div class="flex flex-col truncate">
                    <span class="text-sm font-medium leading-tight truncate"><?php echo e(auth()->user()->name); ?></span>
                    <span class="text-xs text-muted truncate capitalize"><?php echo e(auth()->user()->role); ?></span>
                </div>
            </div>
            
            <!-- Navigasi Utama -->
            <nav class="p-4 flex flex-col gap-1">
                <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md transition-colors <?php echo e(request()->routeIs('dashboard') ? 'text-ink bg-canvas font-medium' : 'text-muted hover:text-ink hover:bg-canvas'); ?>">
                    <i class="ph-fill ph-squares-four text-lg"></i> Dashboard
                </a>
                <a href="<?php echo e(route('surat-masuk')); ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md transition-colors <?php echo e(request()->routeIs('surat-masuk') ? 'text-ink bg-canvas font-medium' : 'text-muted hover:text-ink hover:bg-canvas'); ?>">
                    <i class="ph-fill ph-envelope-simple text-lg"></i> Surat Masuk
                </a>
                <a href="<?php echo e(route('surat-keluar')); ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md transition-colors <?php echo e(request()->routeIs('surat-keluar') ? 'text-ink bg-canvas font-medium' : 'text-muted hover:text-ink hover:bg-canvas'); ?>">
                    <i class="ph-fill ph-paper-plane-tilt text-lg"></i> Surat Keluar
                </a>
                <a href="<?php echo e(route('disposisi')); ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md transition-colors <?php echo e(request()->routeIs('disposisi') ? 'text-ink bg-canvas font-medium' : 'text-muted hover:text-ink hover:bg-canvas'); ?>">
                    <i class="ph-fill ph-files text-lg"></i> Disposisi
                </a>
                <?php if(auth()->user()->hasRole('lurah', 'admin')): ?>
                <a href="<?php echo e(route('audit')); ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md transition-colors <?php echo e(request()->routeIs('audit') ? 'text-ink bg-canvas font-medium' : 'text-muted hover:text-ink hover:bg-canvas'); ?>">
                    <i class="ph-fill ph-shield-check text-lg"></i> Audit Keamanan
                </a>
                <?php endif; ?>
                <?php if(auth()->user()->hasRole('admin')): ?>
                <a href="<?php echo e(route('akun')); ?>" class="flex items-center gap-3 px-3 py-2 text-sm rounded-md transition-colors <?php echo e(request()->routeIs('akun') ? 'text-ink bg-canvas font-medium' : 'text-muted hover:text-ink hover:bg-canvas'); ?>">
                    <i class="ph-fill ph-users-three text-lg"></i> Manajemen Akun
                </a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Tombol Logout (Di Bawah) -->
        <div class="p-4 border-t border-borderline">
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="flex items-center gap-3 px-3 py-2 text-sm text-muted hover:text-ink hover:bg-canvas rounded-md transition-colors w-full text-left">
                    <i class="ph ph-sign-out text-lg"></i> Keluar
                </button>
            </form>
        </div>
    </aside>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-6 md:p-12 relative"
        <?php if($isGuest ?? false): ?>
            style="background-image: linear-gradient(rgba(17,17,17,.45), rgba(17,17,17,.45)), url('/images/kantor-dc.webp'); background-size: cover; background-position: center;"
        <?php endif; ?>
    >
        <div class="max-w-[1500px] mx-auto w-full">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('active'); });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));

        // Dipakai oleh modal tambah/edit surat masuk & surat keluar.
        function previewSelectedRegisterFile(input, imageId, nameId) {
            const image = document.getElementById(imageId);
            const name = document.getElementById(nameId);
            const file = input.files && input.files[0] ? input.files[0] : null;

            image.classList.add('hidden');
            image.removeAttribute('src');
            name.textContent = '';

            if (!file) return;

            name.textContent = 'File dipilih: ' + file.name;
            if (file.type.startsWith('image/')) {
                image.src = URL.createObjectURL(file);
                image.classList.remove('hidden');
            }
        }
    </script>
</body>
</html><?php /**PATH /var/www/resources/views/components/layout.blade.php ENDPATH**/ ?>