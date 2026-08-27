# Setup Docker untuk SIMA

Proyek ini sebenarnya sudah punya `Dockerfile`, `docker-compose.yml`, dan
`docker/nginx/default.conf`, tapi ada beberapa hal yang belum jalan kalau
langsung dipakai. File-file di paket ini menggantikan yang lama dan
memperbaiki masalah berikut:

- **Asset frontend (Vite/Tailwind) belum pernah di-build** — tidak ada
  langkah `npm run build` sama sekali di setup lama, jadi `public/build`
  tidak akan pernah ada dan halaman akan tampil tanpa CSS/JS.
- **Env DB user tidak konsisten** — `.env` kamu pakai `DB_USERNAME=root`
  dengan `DB_PASSWORD` kosong, sedangkan `docker-compose.yml` lama punya
  fallback yang malah membuat password MySQL jadi `root`. Hasilnya app
  gagal konek ke database. Sekarang `docker-compose.yml` membaca
  `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` langsung dari `.env` kamu,
  jadi cuma ada satu sumber kebenaran.
- **Upload file terlalu kecil** — `SuratKeluarController` mengizinkan file
  sampai 4MB, tapi `upload_max_filesize` default PHP cuma 2M. Ditambahkan
  `docker/php/uploads.ini` untuk menaikkan limitnya.
- **Tidak ada migrasi/link storage otomatis** — ditambahkan
  `docker/php/entrypoint.sh` yang menunggu MySQL siap, generate `APP_KEY`
  kalau belum ada, jalankan migrasi, dan `storage:link` otomatis saat
  container start.
- `version: "3.8"` di `docker-compose.yml` dihapus karena sudah usang di
  Compose versi baru.

## 1. Update `.env` kamu

Cukup ubah 2 baris ini di `.env` (jangan pakai `root` sebagai
`DB_USERNAME`, dan jangan biarkan password kosong):

```env
DB_USERNAME=sima
DB_PASSWORD=secret
```

Opsional, biar URL/redirect konsisten dengan port nginx di bawah:

```env
APP_URL=http://localhost:8000
```

## 2. Salin file-file di paket ini ke root project

Struktur yang perlu ditimpa/ditambahkan (path relatif terhadap root
project `SIMA/`):

```
Dockerfile
docker-compose.yml
.dockerignore
docker/nginx/default.conf
docker/php/entrypoint.sh
docker/php/uploads.ini
```

## 3. Jalankan

```bash
docker compose up -d --build
```

- App: http://localhost:8000
- MySQL juga di-expose ke `localhost:3306` kalau mau dicek pakai TablePlus/DBeaver dsb.
- Service `node` cuma jalan sekali (install + `npm run build`) lalu keluar
  — aman kalau mau di-`up` ulang, atau tinggal comment service-nya di
  `docker-compose.yml` setelah asset pertama kali ke-build.

## 4. Isi data awal (opsional)

`DatabaseSeeder` kamu sudah bikin 2 akun default (staf & lurah, password
sama). Untuk menjalankannya:

```bash
docker compose exec app php artisan db:seed
```

atau set `DB_SEED=true` di `.env` sebelum `docker compose up` supaya
otomatis ke-seed saat container pertama kali start.

## Perintah yang sering dipakai

```bash
docker compose logs -f app          # lihat log Laravel/PHP-FPM
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan tinker
docker compose down                 # stop semua
docker compose down -v              # stop + hapus volume MySQL (reset data)
```

## Catatan

- `app` dan `web` mount seluruh folder project (`./:/var/www`) supaya
  perubahan kode PHP/Blade langsung kelihatan tanpa rebuild image.
- Container `node` jalan sebagai root, jadi `node_modules` dan
  `public/build` yang dihasilkan akan dimiliki root di host. Kalau nanti
  mau hapus manual dari host tanpa Docker, mungkin perlu `sudo`.
- Dockerfile tetap multi-stage (composer install + vite build di dalam
  image) supaya kalau suatu saat deploy tanpa bind-mount (mis. ke VPS),
  image-nya tetap jalan sendiri tanpa butuh service `node`.
