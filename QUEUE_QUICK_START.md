# Queue Implementation - Quick Start Guide

## Ringkasan Perubahan

Notifikasi Telegram yang sebelumnya **synchronous** (blocking) sekarang **asynchronous** (background job).

### Sebelum:
```
User create income → Controller process → HTTP ke Telegram API → Response ke user
                    (BLOCKING - lambat jika Telegram API slow)
```

### Sesudah:
```
User create income → Controller process → Job di-queue → Response ke user (INSTANT)
                                              ↓
                                        Queue Worker (background)
                                        HTTP ke Telegram API
```

---

## Setup Instructions (5 langkah)

### Langkah 1: Update `.env`

```env
QUEUE_CONNECTION=database
```

Sudah default di `config/queue.php`, tapi pastikan di `.env` tidak ada yang override.

### Langkah 2: Run Database Migration

```bash
php artisan migrate
```

Ini akan create tabel `jobs` dan `failed_jobs` (sudah ada migration-nya).

### Langkah 3: Verify Code Changes

File yang sudah diubah/dibuat:

```
✓ app/Jobs/SendTelegramNotification.php (NEW)
✓ app/Http/Controllers/IncomeController.php (UPDATED)
✓ Database migration (ALREADY EXISTS)
```

### Langkah 4: Test di Local

Buka 2 terminal:

**Terminal 1 - Development:**
```bash
php artisan serve
```

**Terminal 2 - Queue Worker:**
```bash
php artisan queue:work
```

Sekarang coba create income di UI. Perhatikan:
1. Response langsung cepat (tidak wait Telegram)
2. Di Terminal 2 akan lihat "Processing: App\Jobs\SendTelegramNotification"

### Langkah 5: Setup Production (Optional)

Lihat: `docs/QUEUE_WORKER_SETUP.md` untuk Supervisor setup.

---

## Code Changes Explained

### Perubahan 1: Buat Job

**File:** `app/Jobs/SendTelegramNotification.php`

```php
class SendTelegramNotification implements ShouldQueue
{
    public int $tries = 3;          // Retry 3 kali jika gagal
    public int $timeout = 30;       // Timeout 30 detik
    
    public function __construct(
        private User $user,
        private string $message,
        private ?string $customBotToken = null,
    ) {}

    public function handle(TelegramService $telegramService): void
    {
        $telegramService->notifyUser($this->user, $this->message);
    }

    public function backoff(): array
    {
        return [1, 5, 10];  // Retry delay: 1s, 5s, 10s
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Telegram notification failed', [
            'user_id' => $this->user->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
```

### Perubahan 2: Update Controller

**File:** `app/Http/Controllers/IncomeController.php`

**Sebelum:**
```php
$this->telegram->notifyUser($user, $message);  // Langsung, BLOCKING
return redirect(...);  // User tunggu sebentar
```

**Sesudah:**
```php
SendTelegramNotification::dispatch($user, $message);  // Queue, NON-BLOCKING
return redirect(...);  // User langsung dapat response (INSTANT)
```

---

## Cara Verify Implementasi

### Check 1: Jobs Table

```bash
php artisan tinker

# Lihat ada berapa jobs di queue
>>> DB::table('jobs')->count();
=> 5

# Lihat detail job
>>> DB::table('jobs')->latest()->first();
```

### Check 2: Queue Worker

Jalankan di terminal:
```bash
php artisan queue:work --verbose
```

Output yang diharap:
```
[2024-08-04 10:30:45] Processing: App\Jobs\SendTelegramNotification
[2024-08-04 10:30:46] Processed:  App\Jobs\SendTelegramNotification
```

### Check 3: Failed Jobs

Jika ada error:
```bash
php artisan queue:failed

# Lihat detail failed job
php artisan queue:failed:show {id}

# Retry failed job
php artisan queue:retry {id}
```

### Check 4: Run Tests

```bash
php artisan test tests/Feature/QueueTest.php
```

---

## Commands Reference

### Development

```bash
# Run queue worker
php artisan queue:work

# Run dengan verbose (lihat detail)
php artisan queue:work --verbose

# Run specific queue
php artisan queue:work --queue=telegram

# Process 1 job saja
php artisan queue:work --once
```

### Monitoring

```bash
# Lihat failed jobs
php artisan queue:failed

# Lihat detail
php artisan queue:failed:show 1

# Flush semua jobs
php artisan queue:flush

# Flush failed jobs
php artisan queue:flush --failed
```

### Maintenance

```bash
# Retry semua failed jobs
php artisan queue:retry all

# Retry specific failed job
php artisan queue:retry 1

# Forget specific failed job
php artisan queue:forget 1

# Restart worker (graceful)
php artisan queue:restart
```

### Testing

```bash
# Run queue tests
php artisan test tests/Feature/QueueTest.php

# Run dengan coverage
php artisan test --coverage tests/Feature/QueueTest.php
```

---

## FAQ

### Q: Apakah worker perlu running terus?
A: Ya, untuk process background jobs. Di production, gunakan Supervisor untuk auto-restart.

### Q: Bagaimana jika user tidak set chat ID?
A: Job tetap di-dispatch, tapi `notifyUser()` akan return false. Check method `notifyUser()` di `TelegramService` - sudah handle case ini.

### Q: Berapa lama job diproses?
A: Tergantung queue. Biasanya instant sampai beberapa detik. Jika Telegram API slow, bisa sampai 10-30 detik (timeout).

### Q: Apa yang terjadi jika job gagal?
A: Job di-retry otomatis sesuai `$tries` dengan backoff dari `backoff()`. Setelah semua retry habis, go to `failed_jobs` table dan call `failed()` method.

### Q: Bisa pakai Redis instead of Database?
A: Ya, ubah `QUEUE_CONNECTION=redis` di `.env`. Hanya perlu update itu, code tidak berubah.

### Q: Bagaimana monitor queue di production?
A: 
- Check database: `SELECT COUNT(*) FROM jobs;`
- Check logs: `tail -f /var/log/keuanganku-queue.log`
- Check supervisor: `sudo supervisorctl status`

---

## Troubleshooting

### Issue: "Class SendTelegramNotification not found"
**Solusi:** Run `composer dump-autoload` dan `php artisan clear-cache`

### Issue: Jobs tidak diproses
**Diagnosis:**
```bash
# Check worker running
ps aux | grep queue

# Check config
php artisan config:show queue

# Verify database
php artisan migrate --refresh

# Test worker
php artisan queue:work --once
```

### Issue: Memory leak / high usage
**Solusi:**
```bash
# Restart worker periodically (di Supervisor config)
php artisan queue:restart

# Atau reduce numprocs di Supervisor
# Edit: /etc/supervisor/conf.d/keuanganku-queue.conf
```

### Issue: "SQLSTATE[HY000]: General error: 1366"
**Cause:** Job payload contains invalid UTF-8
**Solusi:** Ensure all strings valid UTF-8 sebelum dispatch

---

## Next Steps

1. **Test locally first:**
   ```bash
   php artisan queue:work --verbose
   ```

2. **Run tests:**
   ```bash
   php artisan test tests/Feature/QueueTest.php
   ```

3. **Deploy ke production:**
   - Setup Supervisor (lihat: `config/supervisor/keuanganku-queue.conf`)
   - Or use: `php artisan queue:work` dalam cron/process manager lain

4. **Monitor:**
   - Check logs
   - Monitor database queue
   - Alert jika failed_jobs accumulating

---

## Documentation Links

- Full Queue Guide: `docs/QUEUE_IMPLEMENTATION_GUIDE.md`
- Job Creation: `docs/JOB_CREATION_GUIDE.md`
- Worker Setup: `docs/QUEUE_WORKER_SETUP.md`
- Tests: `tests/Feature/QueueTest.php`
- Job Code: `app/Jobs/SendTelegramNotification.php`

---

## Support

Jika ada pertanyaan:
1. Check relevant documentation file
2. Check test file untuk usage examples
3. Run `php artisan queue:work --verbose` untuk debug
4. Check database: `SELECT * FROM jobs;` dan `SELECT * FROM failed_jobs;`

