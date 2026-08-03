# Job Creation Guide

## Panduan Membuat Job di Laravel

---

## Apa itu Job?

Job adalah class yang merepresentasikan sebuah task yang akan dijalankan di background/queue. Berbeda dengan controller action yang harus selesai dalam satu request, job bisa dijalankan kapan saja oleh queue worker.

---

## Struktur SendTelegramNotification Job

### 1. Interface ShouldQueue

```php
class SendTelegramNotification implements ShouldQueue
{
    // ...
}
```

Interface ini memberi tahu Laravel bahwa job ini harus di-queue, bukan langsung dijalankan.

**Alternatif:** Jika tidak implement ShouldQueue, job akan synchronous (immediate execution).

### 2. Constructor - Menerima Parameter

```php
public function __construct(
    private User $user,
    private string $message,
    private ?string $customBotToken = null,
) {
}
```

Parameter yang diterima di constructor akan di-serialize dan disimpan di database. Saat job dijalankan, parameter di-reconstruct otomatis.

**Important:** 
- Sebisa mungkin pass Model bukan array/string untuk relationships
- Laravel akan serialize Model ID, bukan seluruh Model data
- Saat handle(), Model di-fetch dari database otomatis

### 3. Properties Penting

```php
public int $tries = 3;          // Jumlah percobaan maksimal
public int $timeout = 30;       // Timeout per job (detik)
public int $delay = 0;          // Delay sebelum eksekusi (detik)
public string $queue = 'default'; // Nama queue
```

**Penjelasan:**
- `$tries` - Jika job gagal, akan di-retry sampai `$tries` kali
- `$timeout` - Jika job belum selesai dalam timeout, dihentikan
- `$delay` - Biasanya 0, tapi bisa untuk schedule delayed jobs
- `$queue` - Untuk prioritization (bisa separate worker per queue)

### 4. Method handle()

```php
public function handle(TelegramService $telegramService): void
{
    // Logic yang dijalankan oleh queue worker
    $success = $telegramService->notifyUser($this->user, $this->message);
}
```

**Poin penting:**
- Method ini dipanggil oleh queue worker
- Dependency injection support (TelegramService auto-resolved)
- Harus `void` atau bisa return anything
- Jika throw exception, job akan di-retry (sesuai $tries)

### 5. Method backoff()

```php
public function backoff(): array
{
    return [1, 5, 10];
}
```

Menentukan delay (dalam detik) antara retry:
- Retry 1 gagal: tunggu 1 detik, retry lagi
- Retry 2 gagal: tunggu 5 detik, retry lagi
- Retry 3 gagal: tunggu 10 detik, final attempt

Jika kosong, menggunakan exponential backoff default.

### 6. Method failed()

```php
public function failed(\Throwable $exception): void
{
    Log::error('Job failed', [
        'exception' => $exception->getMessage(),
        'attempts' => $this->attempts(),
    ]);
}
```

Dipanggil jika job gagal setelah semua percobaan. Bisa untuk:
- Cleanup resources
- Send alert ke admin
- Update status di database
- Log error details

### 7. Method middleware()

```php
public function middleware(): array
{
    return [
        new RateLimit('telegram-notifications', 10, 60),
    ];
}
```

Middleware untuk job ini. Contoh di atas: limit max 10 concurrent executions per minute.

---

## Cara Membuat Job Baru

### Via CLI

```bash
php artisan make:job SendTelegramNotification
```

Ini generate file kosong di `app/Jobs/SendTelegramNotification.php`.

### Manual

Buat file di `app/Jobs/YourJobName.php` dengan struktur seperti di atas.

---

## Cara Dispatch Job

### 1. Dispatch Synchronous (Testing)

```php
SendTelegramNotification::dispatch($user, $message);
```

Default: queue immediately untuk diproses oleh worker.

### 2. Dispatch dengan Delay

```php
SendTelegramNotification::dispatch($user, $message)
    ->delay(now()->addMinutes(5));
```

Job akan diproses 5 menit kemudian.

### 3. Dispatch ke Queue Spesifik

```php
SendTelegramNotification::dispatch($user, $message)
    ->onQueue('high-priority');
```

### 4. Dispatch Chain (Sequential Jobs)

```php
Bus::chain([
    new SendTelegramNotification($user, 'Message 1'),
    new SendTelegramNotification($user, 'Message 2'),
])->dispatch();
```

Jobs dijalankan satu per satu secara berurutan.

### 5. Dispatch Batch (Multiple Jobs)

```php
Bus::batch([
    new SendTelegramNotification($user1, 'Message 1'),
    new SendTelegramNotification($user2, 'Message 2'),
    new SendTelegramNotification($user3, 'Message 3'),
])->dispatch();
```

Jobs dijalankan parallel.

---

## Job Lifecycle

```
1. Job::dispatch() 
   ↓
2. Diserialisasi & disimpan di tabel `jobs`
   ↓
3. Queue worker pick up job
   ↓
4. Deserialisasi parameter
   ↓
5. Call handle() method
   ↓
6. Jika sukses: delete dari tabel jobs
   ↓
7. Jika gagal: increment attempts, re-queue atau pindah ke failed_jobs
```

---

## Best Practices

1. **Keep jobs simple and focused**
   - Satu job = satu responsibility
   - Hindari complex nested logic

2. **Set realistic retry counts**
   - 3 retries untuk most cases
   - Network request: 5 retries
   - Critical job: 10 retries

3. **Use meaningful queue names**
   - `email` untuk email jobs
   - `telegram` untuk Telegram notifications
   - `heavy` untuk long-running jobs

4. **Log everything**
   - Log di handle() saat mulai
   - Log di handle() saat sukses
   - Log di failed() saat error

5. **Avoid large payloads**
   - Parameter diserialisasi, hindari large objects
   - Pass Model ID bukan Model instance (mostly)

6. **Handle exceptions gracefully**
   - Jangan throw exception sembarangan
   - Catch dan handle, atau throw exception yang meaningful

---

## Next Steps

1. Lihat: `app/Jobs/SendTelegramNotification.php` (implementasi lengkap)
2. Lanjut: Update Controller untuk menggunakan job
3. Check: `tests/Feature/QueueTest.php` untuk testing

