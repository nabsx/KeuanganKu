# Laravel Queue Implementation Guide

## Panduan Lengkap: Implementasi Queue untuk Notifikasi Telegram

Dokumen ini memberikan panduan step-by-step untuk memindahkan notifikasi Telegram dari synchronous ke asynchronous menggunakan Laravel Queue.

---

## Bagian 1: Memahami Queue Connection

### Database vs Redis - Kapan Menggunakan Mana?

| Aspek | Database Queue | Redis Queue |
|-------|---|---|
| **Setup** | Lebih mudah, langsung bisa pakai | Perlu install Redis tambahan |
| **Performance** | Lambat untuk volume tinggi | Cepat, optimal untuk ribuan job/detik |
| **Persistence** | Punya (data tersimpan di DB) | Ada, tapi volatile jika crash |
| **Maintenance** | Minimal | Butuh monitoring lebih |
| **Use Case** | Aplikasi kecil/medium, development | Production, high-volume jobs |
| **Scaling** | Terbatas | Scalable, bisa multiple workers |

### Rekomendasi untuk KeuanganKu:

**Pilih Database Queue karena:**
- Aplikasi finance personal, volume job rendah
- Setup lebih simpel tanpa dependency tambahan
- Data Telegram notification sudah aman di database
- Mudah di-debug dan monitor lewat database

---

## Bagian 2: Konfigurasi Queue

### Step 1: Update `.env`

```env
# Queue Configuration
QUEUE_CONNECTION=database

# Optional: untuk Redis (jika ingin upgrade nanti)
# QUEUE_CONNECTION=redis
# REDIS_QUEUE_CONNECTION=default
# REDIS_QUEUE=default
# REDIS_QUEUE_RETRY_AFTER=90
```

### Step 2: Verifikasi `config/queue.php`

File sudah ada di project. Default connection sudah set ke `database`:

```php
'default' => env('QUEUE_CONNECTION', 'database'),

'connections' => [
    'database' => [
        'driver' => 'database',
        'connection' => env('DB_QUEUE_CONNECTION'),
        'table' => env('DB_QUEUE_TABLE', 'jobs'),
        'queue' => env('DB_QUEUE', 'default'),
        'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
        'after_commit' => false,
    ],
    // ... connections lainnya
]
```

**Penjelasan:**
- `driver: database` - Gunakan database sebagai queue storage
- `table: jobs` - Tabel yang menyimpan job queue
- `retry_after: 90` - Retry job jika tidak selesai dalam 90 detik
- `after_commit: false` - Langsung queue tanpa menunggu transaction commit

---

## Bagian 3: Database Migration untuk Queue

### Step 1: Generate Migration Table

Jalankan command untuk generate tabel queue (jika belum ada):

```bash
php artisan queue:table
```

Perintah ini akan membuat file migration baru di `database/migrations/`. Jika sudah ada (cek di project), skip step ini.

### Step 2: Run Migration

```bash
php artisan migrate
```

Ini akan membuat tabel `jobs` dan `failed_jobs` di database.

**Struktur tabel `jobs`:**
- `id` - Primary key
- `queue` - Nama queue (default: "default")
- `payload` - Serialized job data (JSON)
- `attempts` - Jumlah percobaan
- `reserved_at` - Timestamp saat job mulai diproses
- `available_at` - Waktu job tersedia diproses
- `created_at` - Timestamp saat job dibuat

---

## Bagian 4: Environment Variables & Config

### Tambahkan ke `.env`

```env
# Telegram Configuration (existing)
TELEGRAM_BOT_TOKEN=your_bot_token_here

# Queue Configuration
QUEUE_CONNECTION=database
```

### Update `config/services.php`

Tambahkan konfigurasi Telegram di `config/services.php`:

```php
'telegram' => [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'timeout' => 10,
    'retries' => 3,
],
```

Ini memungkinkan job untuk mengakses konfigurasi Telegram secara terpusat.

---

## Bagian 5: Best Practices

1. **Jangan hardcode credentials**
   - Selalu gunakan `env()` atau `config()`
   - Simpan sensitive data di `.env`

2. **Set timeout sesuai kebutuhan**
   - Telegram API timeout: 10 detik
   - Queue retry_after: 90 detik

3. **Gunakan after_commit = true untuk data consistency**
   - Untuk transact yang penting, set `after_commit: true`
   - Memastikan job hanya di-queue setelah database transaction commit

4. **Monitor queue health**
   - Periksa tabel `jobs` secara berkala
   - Monitor `failed_jobs` untuk debugging

---

## Next Steps

1. Lanjut ke: **Bagian 6: Membuat Job**
2. Lihat: `docs/JOB_CREATION_GUIDE.md`
3. Check: `tests/Feature/QueueTest.php` untuk testing
