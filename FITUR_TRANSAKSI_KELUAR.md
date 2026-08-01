# Fitur Transaksi Manual (Uang Keluar)

## Ringkasan
Fitur ini memungkinkan pengguna untuk mencatat pengeluaran/penarikan uang dari wallet secara manual, misalnya untuk:
- Cicilan (mobil, rumah, dll)
- Biaya hidup bulanan
- Penarikan tunai
- Pengeluaran lainnya

## File-File yang Ditambahkan

### 1. Controller: `app/Http/Controllers/ManualTransactionController.php`
```php
- Method create(): Menampilkan form transaksi keluar
- Method store(): Memproses dan menyimpan transaksi
```

**Fitur:**
- Validasi saldo (mencegah saldo menjadi negatif)
- Menggunakan DB transaction untuk atomicity
- Injeksi `TelegramService` untuk notifikasi
- Authorization check untuk memastikan user hanya bisa edit wallet miliknya

### 2. Form Request: `app/Http/Requests/StoreManualTransactionRequest.php`
Validasi input:
- `amount` (required, numeric, > 0) - Jumlah uang keluar
- `description` (required, max 255) - Keterangan transaksi
- `transaction_date` (required, date_format) - Tanggal transaksi

### 3. Blade View: `resources/views/transactions/create.blade.php`
Form UI dengan:
- Input jumlah nominal
- Input keterangan transaksi
- Input tanggal transaksi
- Tampilan saldo wallet saat ini
- Tombol submit & batal
- Styling dengan Tailwind CSS

### 4. Routes: `routes/web.php`
```php
GET  /wallets/{wallet}/transaksi-keluar    → transactions.create
POST /wallets/{wallet}/transaksi-keluar    → transactions.store
```

### 5. UI Update: `resources/views/wallets/show.blade.php`
Tombol "+ Transaksi Keluar" ditambahkan di halaman detail wallet, di bagian saldo.

## Notifikasi Telegram

### Format Notifikasi
Ketika transaksi berhasil dicatat, notifikasi Telegram akan dikirim dengan format:

```
❌ Pengeluaran dari wallet [Nama Wallet] berhasil dicatat!
Keterangan: [Deskripsi transaksi]
Nominal: Rp [Amount]
Saldo sekarang: Rp [Saldo setelah transaksi]
```

### Syarat Pengiriman
Notifikasi hanya dikirim jika:
1. User sudah aktifkan notifikasi Telegram (di menu Pengaturan Telegram)
2. User sudah set Chat ID untuk Telegram
3. Bot token sudah dikonfigurasi di `.env` (TELEGRAM_BOT_TOKEN)

### Integrasi
- Menggunakan `TelegramService` yang sudah ada
- Tidak akan menggagalkan proses transaksi jika pengiriman notifikasi gagal
- Error pengiriman dicatat di Laravel log

## Database

### Struktur Tabel `wallet_transactions`
Kolom yang digunakan untuk transaksi manual:
- `wallet_id` - ID wallet
- `user_id` - ID user
- `type` - 'out' (untuk pengeluaran)
- `amount` - Nominal pengeluaran
- `balance_after` - Saldo setelah transaksi
- `source` - 'manual' (untuk transaksi manual)
- `description` - Keterangan transaksi
- `transaction_date` - Tanggal transaksi

## Alur Penggunaan

1. **Akses Form**
   - Buka halaman detail wallet (`/wallets/{id}`)
   - Klik tombol "+ Transaksi Keluar"

2. **Isi Form**
   - Input jumlah uang yang dikeluarkan
   - Input keterangan (cicilan mobil, dll)
   - Pilih tanggal transaksi

3. **Submit**
   - Sistem validasi:
     - Cek saldo cukup atau tidak
     - Cek input sudah valid atau tidak
   - Jika valid: Proses transaksi
   - Jika invalid: Tampilkan error

4. **Hasil**
   - Balance wallet otomatis berkurang
   - Record transaksi tersimpan di database
   - Notifikasi Telegram dikirim ke user (jika aktif)
   - Redirect ke halaman detail wallet dengan pesan sukses

## Keamanan

1. **Authorization** - Hanya wallet owner yang bisa menambah transaksi
2. **Validation** - Semua input divalidasi di backend
3. **Atomicity** - DB transaction memastikan data konsisten
4. **Saldo Check** - Mencegah saldo negatif
5. **Logging** - Semua error dicatat di Laravel log

## Testing

### Manual Test Checklist
- [ ] Form dapat diakses
- [ ] Validasi bekerja (jumlah harus numeric, desc harus isi, dll)
- [ ] Saldo berkurang setelah submit
- [ ] Record transaksi tersimpan di database
- [ ] Notifikasi Telegram diterima (jika aktif)
- [ ] Redirect ke halaman wallet dengan pesan sukses
- [ ] Tombol batal membawa kembali ke halaman wallet
