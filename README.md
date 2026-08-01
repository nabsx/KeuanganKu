# Aplikasi Catat Pendapatan & Manajemen Wallet

Aplikasi web berbasis **Laravel + MySQL** untuk mencatat pendapatan, mengatur beberapa
wallet/tabungan, membagi pendapatan ke wallet secara otomatis berdasarkan persentase,
mencatat riwayat tiap wallet, dan mengirim notifikasi ke Telegram.

Paket ini berisi **kode tambahan (delta)** yang perlu digabungkan ke instalasi Laravel
baru — bukan project Laravel lengkap dengan `vendor/` — supaya Anda selalu mendapatkan
versi framework Laravel terbaru dan bersih saat `composer install`.

---

## 1. Struktur Isi Paket

```
app/
  Http/
    Controllers/
      Auth/AuthController.php
      Controller.php              (menambahkan trait AuthorizesRequests)
      DashboardController.php
      WalletController.php
      IncomeController.php
      WalletAllocationController.php
      TelegramSettingController.php
    Requests/
      StoreWalletRequest.php
      UpdateWalletRequest.php
      StoreIncomeRequest.php
      UpdateAllocationRequest.php
      UpdateTelegramSettingRequest.php
  Models/
    User.php
    Wallet.php
    WalletAllocation.php
    Income.php
    WalletTransaction.php
    TelegramSetting.php
  Services/
    TelegramService.php
    IncomeAllocationService.php
  Policies/
    WalletPolicy.php
config/
  telegram.php
database/
  migrations/
    2025_01_01_000001_create_wallets_table.php
    2025_01_01_000002_create_wallet_allocations_table.php
    2025_01_01_000003_create_incomes_table.php
    2025_01_01_000004_create_wallet_transactions_table.php
    2025_01_01_000005_create_telegram_settings_table.php
routes/
  web.php
resources/
  views/
    layouts/app.blade.php
    auth/login.blade.php, register.blade.php
    dashboard.blade.php
    wallets/index.blade.php, create.blade.php, edit.blade.php, show.blade.php
    incomes/index.blade.php, create.blade.php
    allocations/edit.blade.php
    telegram/edit.blade.php
env-tambahan.txt   (baris .env yang perlu ditambahkan)
```

---

## 2. Cara Menjalankan (dari nol)

### a. Buat project Laravel baru

```bash
composer create-project laravel/laravel catat-pendapatan
cd catat-pendapatan
```

### b. Salin isi paket ini ke dalam project

Salin (overwrite) folder `app/`, `config/`, `database/migrations/`, `routes/web.php`,
dan `resources/views/` dari paket ini ke dalam folder project Laravel yang baru dibuat.
Semua file bawaan Laravel yang lain (bootstrap, public, dsb.) **tidak perlu diubah**.

```bash
# jalankan dari folder hasil ekstrak paket ini, sesuaikan path tujuan
cp -r app/*        ../catat-pendapatan/app/
cp -r config/*      ../catat-pendapatan/config/
cp -r database/migrations/* ../catat-pendapatan/database/migrations/
cp routes/web.php   ../catat-pendapatan/routes/web.php
cp -r resources/views/* ../catat-pendapatan/resources/views/
```

### c. Konfigurasi `.env`

Buka file `.env` di project Laravel Anda, lalu isi/samakan dengan isi
`env-tambahan.txt` dari paket ini (koneksi database MySQL + token bot Telegram).

Buat database MySQL kosong sesuai nama di `DB_DATABASE`, misalnya lewat:

```sql
CREATE DATABASE catat_pendapatan;
```

### d. Install dependency & migrasi database

```bash
composer install
php artisan key:generate
php artisan migrate
```

### e. Jalankan aplikasi

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser. Tidak perlu `npm install`/`npm run build`
karena tampilan menggunakan Tailwind CSS via CDN — langsung jalan tanpa proses build.

---

## 3. Alur Pemakaian

1. **Daftar akun** di `/register`, lalu login otomatis.
2. **Tambah wallet** di menu *Wallet* (mis. Tabungan, Cicilan, Servis Motor, Uang Makan,
   Dana Darurat). Wallet baru otomatis mendapat persentase awal 0%.
3. Buka menu **Persentase**, atur persentase tiap wallet. Total harus **tepat 100%**
   (ada indikator total & validasi real-time) sebelum bisa disimpan.
4. Buka menu **Pendapatan → Catat Pendapatan**, isi tanggal, nominal, sumber, dan
   catatan. Setelah disimpan, sistem otomatis membagi nominal ke semua wallet sesuai
   persentase, mencatatnya sebagai riwayat transaksi wallet, dan mengirim notifikasi
   Telegram (jika sudah diaktifkan).
5. Buka menu **Wallet → Riwayat** pada wallet tertentu untuk melihat histori
   transaksi masuk/keluar wallet tersebut.
6. Buka menu **Telegram** untuk mengaktifkan notifikasi (lihat panduan di halaman
   tersebut untuk mendapatkan Chat ID dari bot BotFather Anda).

---

## 4. Aturan Bisnis Penting

- **Persentase wallet wajib total tepat 100%.** Sistem menolak menyimpan persentase
  atau mencatat pendapatan baru jika totalnya kurang atau lebih dari 100%, dan
  menampilkan pesan error yang jelas + peringatan Telegram.
- **Pembagian nominal otomatis** dilakukan oleh `App\Services\IncomeAllocationService`.
  Setiap wallet (kecuali yang terakhir dalam urutan) dibulatkan 2 desimal dari
  `nominal x persentase / 100`; wallet **terakhir** menerima sisa dari total nominal
  dikurangi yang sudah dibagi ke wallet lain. Dengan begini total seluruh alokasi
  selalu **presis sama** dengan nominal pendapatan, tidak ada selisih pembulatan yang
  hilang atau berlebih.
- **Isolasi data antar user**: semua query di controller di-scope lewat relasi
  `Auth::user()->wallets()`/`incomes()`, dan kepemilikan wallet individual dicek lagi
  lewat `App\Policies\WalletPolicy` (`$this->authorize(...)`) di setiap aksi
  edit/lihat/hapus wallet — sehingga user lain tidak bisa mengakses data yang bukan
  miliknya meskipun menebak ID di URL.
- **Password di-hash** otomatis lewat cast `'password' => 'hashed'` pada model `User`
  dan validasi kekuatan password (`Password::min(8)->letters()->numbers()`) saat
  registrasi.
- Semua route utama dibungkus middleware `auth` bawaan Laravel; tamu yang belum login
  otomatis diarahkan ke halaman login.

---

## 5. Integrasi Telegram

- Notifikasi dikirim lewat `App\Services\TelegramService`, memanggil endpoint resmi
  `https://api.telegram.org/bot<TOKEN>/sendMessage` menggunakan `Http` client bawaan
  Laravel — tidak perlu package tambahan.
- Token bot bersifat **global** (satu bot untuk seluruh aplikasi), disimpan di
  `TELEGRAM_BOT_TOKEN` pada `.env` dan dibaca lewat `config/telegram.php`.
- Chat ID bersifat **per user**, disimpan di tabel `telegram_settings` dan diatur
  lewat menu *Telegram* di aplikasi (lengkap dengan tombol kirim pesan uji coba).
- Notifikasi dikirim otomatis saat:
  - pendapatan baru berhasil dicatat,
  - setiap wallet menerima alokasi dana,
  - persentase wallet berhasil diperbarui,
  - ada percobaan menyimpan persentase/pendapatan yang totalnya tidak 100% (peringatan).
- Pengiriman dibungkus `try-catch` dan tidak pernah menggagalkan proses utama —
  jika Telegram gagal terkirim (token salah, tidak ada koneksi, dll.), pencatatan
  pendapatan/wallet tetap berhasil, hanya notifikasinya yang terlewat (dicatat di log).
- **Pengembangan lanjutan (opsional):** jika volume notifikasi besar, ubah
  `TelegramService::send()` untuk dipanggil dari dalam sebuah Job
  (`implements ShouldQueue`) agar pengiriman berjalan asinkron lewat
  `php artisan queue:work`, tanpa menunggu respons Telegram saat request HTTP.

---

## 6. Struktur Database & Relasi

| Tabel | Keterangan |
|---|---|
| `users` | Data akun (bawaan Laravel) |
| `wallets` | Wallet/tabungan milik user (`user_id`, `name`, `balance`, ...) |
| `wallet_allocations` | Persentase pembagian per wallet (1 wallet = 1 baris, unique `wallet_id`) |
| `incomes` | Catatan pendapatan (`date`, `amount`, `source`, `note`) |
| `wallet_transactions` | Riwayat mutasi tiap wallet (`type` in/out, `amount`, `balance_after`, terhubung ke `income_id` jika berasal dari alokasi otomatis) |
| `telegram_settings` | Chat ID & status aktif notifikasi Telegram per user |

Relasi:
- `User hasMany Wallet`, `User hasMany Income`, `User hasOne TelegramSetting`
- `Wallet hasOne WalletAllocation`, `Wallet hasMany WalletTransaction`
- `Income hasMany WalletTransaction` (satu pendapatan bisa menghasilkan banyak
  transaksi alokasi, satu per wallet)

---

## 7. Pengembangan Lanjutan yang Disarankan

- Tambahkan fitur transaksi **manual** (uang keluar dari wallet, mis. dipakai untuk
  cicilan) — struktur `wallet_transactions` sudah mendukung `type = 'out'`, tinggal
  dibuat controller & form baru.
- Tambahkan verifikasi email (`MustVerifyEmail`) bila aplikasi akan dipakai publik.
- Tambahkan `php artisan queue:work` + Job untuk notifikasi Telegram bervolume tinggi.
- Tambahkan test otomatis (`php artisan test`) untuk logic pembagian persentase di
  `IncomeAllocationService`, terutama untuk kasus pembulatan.
