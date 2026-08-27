# SIMA — Sistem Informasi Manajemen Arsip

Aplikasi web pencatatan arsip surat untuk **Kelurahan Dungus Cariang**, Kecamatan Andir, Kota Bandung. Dibangun sebagai proyek Kerja Praktik.

SIMA mendigitalkan buku agenda register surat (masuk, keluar, keputusan) yang selama ini dicatat manual, dilengkapi fitur disposisi surat dan audit keamanan.

## Fitur

| Modul | Deskripsi |
|-------|-----------|
| **Surat Masuk** | CRUD register surat masuk: nomor urut, tanggal, nomor surat, pengirim, perihal, keterangan, upload dokumen (PDF/gambar) |
| **Surat Keluar** | CRUD register surat keluar: nomor urut, tanggal, nomor surat, tujuan, perihal, keterangan, upload dokumen |
| **Surat Keputusan** | CRUD register SK: nomor urut, nomor SK, tanggal SK, perihal, keterangan, upload dokumen |
| **Disposisi** | Lurah membuat disposisi dari surat masuk ke Kasi/Sekretaris. Staf menandai selesai |
| **Audit Keamanan** | Log aktivitas login, logout, dan setiap mutasi data. Filter berdasarkan event, tanggal, keyword |
| **Manajemen Akun** | Admin mengelola akun pengguna (staf, lurah, admin) |
| **Export Excel** | Setiap register bisa diexport ke file `.xlsx` (PhpSpreadsheet) |
| **Pratinjau Dokumen** | Preview PDF dan gambar langsung di browser tanpa download |

## Role & Hak Akses

| Role | Hak Akses |
|------|-----------|
| **Staf** | CRUD surat (masuk, keluar, keputusan), tandai disposisi selesai |
| **Lurah** | Buat & edit disposisi, lihat audit keamanan |
| **Admin** | Semua hak staf + lurah + manajemen akun |

## Tech Stack

- **Backend:** Laravel 11, PHP 8.2+
- **Database:** MySQL (Docker) / SQLite (lokal)
- **Frontend:** Blade templates, Tailwind CSS (CDN), Phosphor Icons
- **Deployment:** Docker + Docker Compose

## Instalasi Lokal

```bash
# 1. Clone & install dependencies
git clone https://github.com/rayhanfsy/SIMA.git && cd SIMA
composer install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Konfigurasi database di .env
#    Untuk SQLite: DB_CONNECTION=sqlite
#    Untuk MySQL: isi DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 4. Migrasi & seed
php artisan migrate
php artisan db:seed   # jika ada seeder

# 5. Storage link (untuk upload dokumen)
php artisan storage:link

# 6. Jalankan
php artisan serve
```

Akses di `http://localhost:8000`.

## Docker

```bash
docker-compose up -d
```

Detail konfigurasi Docker lihat [`DOCKER_SETUP.md`](DOCKER_SETUP.md).

## Struktur Direktori

```
app/
├── Http/Controllers/
│   ├── SuratMasukController.php
│   ├── SuratKeluarController.php
│   ├── SuratKeputusanController.php
│   ├── DisposisiController.php
│   ├── AuditLogController.php
│   └── AccountController.php
├── Models/
│   ├── SuratMasuk.php
│   ├── SuratKeluar.php
│   ├── SuratKeputusan.php
│   ├── Disposisi.php
│   ├── AuditLog.php
│   └── User.php
└── Support/
    └── ExcelExport.php        # Export Excel tanpa dependency

resources/views/
├── dashboard.blade.php
├── akun.blade.php
├── audit.blade.php
└── surat/
    ├── masuk.blade.php
    ├── keluar.blade.php
    ├── keputusan.blade.php
    ├── disposisi.blade.php
    └── modals/                # Modal form tambah/edit
```

## Lisensi

Proyek Kerja Praktik — Kelurahan Dungus Cariang, Bandung.
