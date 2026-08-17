<?php
// SIMA/routes/web.php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AccountController;
use App\Models\AuditLog;

// ----------------------------------------------------
// GUEST ROUTES (Belum Login)
// ----------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        session(['captcha_ans' => $num1 + $num2]);
        
        return view('auth.login', compact('num1', 'num2'));
    })->name('login');

    Route::post('/login', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required|numeric|in:' . session('captcha_ans')
        ], [
            'captcha.in' => 'Jawaban matematika salah.'
        ]);
        
        // Logika Autentikasi Bawaan Laravel
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            AuditLog::log('LOGIN.SUCCESS', 'Pengguna berhasil masuk ke sistem.', auth()->id(), $request->ip());
            return redirect()->intended('/');
        }

        AuditLog::log('LOGIN.FAILED', 'Login gagal karena kredensial tidak valid atau akun tidak aktif.', null, $request->ip());
        return back()->withErrors(['email' => 'Kredensial tidak valid.']);
    })->name('login.post');
});

// ----------------------------------------------------
// PROTECTED ROUTES (Wajib Login)
// ----------------------------------------------------
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/', function () { 
        return view('dashboard', [
            'masuk' => \App\Models\SuratMasuk::count(),
            'keluar' => \App\Models\SuratKeluar::count(),
            'terbaru' => \App\Models\SuratMasuk::latest()->take(5)->get()
        ]); 
    })->name('dashboard');

    // Logout
    Route::post('/logout', function (Request $request) {
        AuditLog::log('LOGOUT', 'Pengguna keluar dari sistem.', auth()->id(), $request->ip());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    // Surat Masuk
    Route::get('/surat-masuk', [SuratMasukController::class, 'index'])->name('surat-masuk');
    Route::post('/surat-masuk', [SuratMasukController::class, 'store'])->name('surat-masuk.store');
    Route::put('/surat-masuk/{suratMasuk}', [SuratMasukController::class, 'update'])->name('surat-masuk.update');
    Route::get('/surat-masuk/{suratMasuk}/file', [SuratMasukController::class, 'file'])->name('surat-masuk.file');
    Route::delete('/surat-masuk/{suratMasuk}', [SuratMasukController::class, 'destroy'])->name('surat-masuk.destroy');

    // Surat Keluar & Disposisi (View Only Sementara)
    // Ganti route surat keluar menjadi ini:
    Route::get('/surat-keluar', [SuratKeluarController::class, 'index'])->name('surat-keluar');
    Route::post('/surat-keluar', [SuratKeluarController::class, 'store'])->name('surat-keluar.store');
    Route::put('/surat-keluar/{suratKeluar}', [SuratKeluarController::class, 'update'])->name('surat-keluar.update');
    Route::get('/surat-keluar/{suratKeluar}/file', [SuratKeluarController::class, 'file'])->name('surat-keluar.file');
    Route::delete('/surat-keluar/{suratKeluar}', [SuratKeluarController::class, 'destroy'])->name('surat-keluar.destroy');

    Route::get('/disposisi', [\App\Http\Controllers\DisposisiController::class, 'index'])->name('disposisi');
    Route::post('/disposisi', [\App\Http\Controllers\DisposisiController::class, 'store'])->name('disposisi.store');
    Route::patch('/disposisi/{disposisi}/selesai', [\App\Http\Controllers\DisposisiController::class, 'selesai'])->name('disposisi.selesai');

    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit');

    // Manajemen Akun (khusus admin)
    Route::get('/akun', [AccountController::class, 'index'])->name('akun');
    Route::post('/akun', [AccountController::class, 'store'])->name('akun.store');
    Route::put('/akun/{akun}', [AccountController::class, 'update'])->name('akun.update');
    Route::delete('/akun/{akun}', [AccountController::class, 'destroy'])->name('akun.destroy');
});