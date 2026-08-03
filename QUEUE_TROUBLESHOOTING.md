# Queue Troubleshooting Guide

## Problem: Web Masih Loading Menunggu Telegram Terkirim

### Root Cause
Web loading lama berarti notifikasi diproses secara **synchronous** (blocking) bukan **asynchronous** (background).

Hal ini terjadi karena:
1. `QUEUE_CONNECTION` belum di-set di `.env`
2. Queue worker belum berjalan
3. Database belum di-migrate

---

## Solution Checklist

### Step 1: Verify Environment Configuration ✓
Pastikan `.env.development.local` memiliki:
```
QUEUE_CONNECTION=database
```

**Check:**
```bash
grep QUEUE_CONNECTION .env.development.local
```

Expected output:
```
QUEUE_CONNECTION=database
```

---

### Step 2: Run Database Migration
Jobs table diperlukan untuk menyimpan queue data.

```bash
php artisan migrate
```

Check tables:
```bash
php artisan tinker
>>> DB::table('jobs')->count()
=> 0
>>> DB::table('failed_jobs')->count()
=> 0
```

---

### Step 3: Start Queue Worker (PENTING!)

**Terminal 1** - Run Laravel server:
```bash
php artisan serve
```

**Terminal 2** - Run Queue Worker (BUKA TERMINAL BARU):
```bash
php artisan queue:work --verbose
```

Output harus seperti ini:
```
Starting queue worker...
Processing jobs from the [default] queue
[2025-08-04 10:30:45] Processing: App\Jobs\SendTelegramNotification
[2025-08-04 10:30:46] Processing completed!
```

---

### Step 4: Test Flow

1. Create income di web (Terminal 3):
   ```bash
   curl -X POST http://localhost:8000/incomes \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "source=Gaji&amount=5000000&date=2025-08-04"
   ```

2. Lihat response di Terminal 1:
   - Web harus return **instantly** (<100ms)
   - Tidak boleh waiting untuk Telegram

3. Lihat job diproses di Terminal 2:
   ```
   [2025-08-04 10:35:22] Processing: App\Jobs\SendTelegramNotification
   [2025-08-04 10:35:23] Processing completed!
   ```

---

## Debugging Commands

### Check Queue Status
```bash
# Lihat jobs yang pending
php artisan queue:work --once

# Lihat failed jobs
php artisan queue:failed

# Count jobs in queue
php artisan tinker
>>> DB::table('jobs')->count()
```

### Monitor in Real-Time
```bash
# Terminal 2 - dengan verbose
php artisan queue:work --verbose

# Atau dengan daemon mode
php artisan queue:work --daemon
```

### Check Job Details
```bash
php artisan tinker
>>> $job = DB::table('jobs')->first();
>>> $job->payload
>>> DB::table('jobs')->count()
```

### Clear Failed Jobs
```bash
php artisan queue:flush
php artisan queue:retry all
```

---

## Verification Steps

Setelah setting selesai, verifikasi dengan test:

```bash
php artisan test tests/Feature/QueueTest.php
```

Semua test harus **PASS**:
```
Tests:  12 passed
```

---

## Common Issues & Solutions

### Issue 1: "Call to undefined method dispatch()"
**Cause:** Job class tidak implement `ShouldQueue`

**Fix:** Pastikan `SendTelegramNotification` punya:
```php
implements ShouldQueue
use Queueable;
```

---

### Issue 2: Queue Worker Tidak Terima Job
**Cause:** Worker tidak jalan atau wrong queue name

**Fix:**
```bash
# Terminal 2 - cek queue name
php artisan queue:work default --verbose

# Atau gunakan --once untuk process 1 job
php artisan queue:work --once
```

---

### Issue 3: Job Stuck di Database
**Cause:** Reserved jobs yang tidak completed

**Fix:**
```bash
# Retry reserved jobs
php artisan queue:restart

# Atau clear semuanya
php artisan queue:flush
```

---

### Issue 4: Jobs Masih Sync (Blocking)
**Cause:** `QUEUE_CONNECTION` di-set ke `sync` bukan `database`

**Check:**
```bash
php artisan tinker
>>> config('queue.default')
=> "database"
```

If shows `sync`, update `.env`:
```
QUEUE_CONNECTION=database
```

Then restart server.

---

## Production Checklist

Sebelum go to production, pastikan:

- [ ] `QUEUE_CONNECTION=database` di `.env`
- [ ] Database migration sudah run
- [ ] Supervisor config di-setup
- [ ] Queue worker auto-restart enabled
- [ ] Failed jobs monitoring aktif
- [ ] Log files writable
- [ ] Enough disk space untuk database

---

## Performance Targets

Setelah setup benar:

| Metric | Before | After |
|--------|--------|-------|
| Response Time | 3-5 seconds | <100ms |
| User Wait | Blocking | Instant |
| Reliability | Fails if API down | Retries 3x |
| Throughput | Sequential | Parallel |

---

## Still Having Issues?

1. Check logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. Check queue worker logs:
   ```bash
   php artisan queue:work --verbose
   ```

3. Check database:
   ```bash
   php artisan tinker
   >>> DB::select('SELECT * FROM jobs;')
   >>> DB::select('SELECT * FROM failed_jobs;')
   ```

4. Verify environment:
   ```bash
   php artisan tinker
   >>> config('queue')
   ```

5. Run test:
   ```bash
   php artisan test tests/Feature/QueueTest.php --verbose
   ```
