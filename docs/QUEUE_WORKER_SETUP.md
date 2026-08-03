# Queue Worker Setup Guide

## Panduan Menjalankan Queue Worker

---

## Bagian 1: Local Development

### Step 1: Run Queue Worker

Jalankan command di terminal:

```bash
php artisan queue:work
```

Ini akan mulai worker yang listen ke queue `jobs` table dan process jobs satu per satu.

**Output yang akan dilihat:**

```
[2024-08-04 10:30:45] Processing: App\Jobs\SendTelegramNotification
[2024-08-04 10:30:46] Processed:  App\Jobs\SendTelegramNotification
```

### Step 2: Queue Worker dalam Separate Terminal

Disarankan jalankan di terminal terpisah agar bisa tetap develop di terminal lain:

```bash
# Terminal 1 - Development
php artisan serve

# Terminal 2 - Queue Worker
php artisan queue:work
```

### Step 3: Monitor Queue

Untuk lihat status queue dalam real-time:

```bash
# Lihat jumlah jobs di queue
php artisan queue:failed

# Lihat job details (untuk debug)
SELECT * FROM jobs;
SELECT * FROM failed_jobs;
```

### Optional: Run dengan Specific Queue

Jika ingin process hanya queue tertentu:

```bash
php artisan queue:work --queue=telegram
```

---

## Bagian 2: Production Setup dengan Supervisor

### Apa itu Supervisor?

Supervisor adalah process manager untuk Linux/Unix. Gunanya:
- Jalankan queue worker sebagai background daemon
- Auto-restart jika worker crash
- Monitor multiple workers
- Setup logging

### Step 1: Install Supervisor

```bash
# Ubuntu/Debian
sudo apt-get install supervisor

# CentOS/RHEL
sudo yum install supervisor
```

### Step 2: Create Supervisor Configuration

Buat file konfigurasi `/etc/supervisor/conf.d/keuanganku-queue.conf`:

```ini
[program:keuanganku-queue]
; Program name
process_name=%(program_name)s_%(process_num)02d

; Command to run
command=php /path/to/keuanganku/artisan queue:work database --sleep=3 --tries=3 --timeout=30

; Directory
directory=/path/to/keuanganku

; Run as user
user=www-data

; Number of processes (parallel workers)
numprocs=2

; Auto restart
autostart=true
autorestart=true

; Restart if memory exceeds
memorymax=512

; Redirect output
stdout_logfile=/var/log/keuanganku-queue.log
stderr_logfile=/var/log/keuanganku-queue-error.log

; Keep logs
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5

; Priority (higher runs first)
priority=999

; Stop signal
stopsignal=TERM
stopasgroup=true
killasgroup=true
```

**Penjelasan:**
- `numprocs=2` - Jalankan 2 worker secara parallel (sesuaikan dengan CPU cores)
- `sleep=3` - Worker sleep 3 detik jika tidak ada job
- `tries=3` - Retry 3 kali jika job gagal
- `timeout=30` - Timeout 30 detik per job
- `memorymax=512` - Restart jika memory > 512MB

### Step 3: Register dengan Supervisor

```bash
# Read config files (pull latest)
sudo supervisorctl reread

# Update running programs
sudo supervisorctl update

# Start program
sudo supervisorctl start keuanganku-queue

# Check status
sudo supervisorctl status
```

**Output yang diharap:**

```
keuanganku-queue:keuanganku-queue_00   RUNNING   pid 1234, uptime 0:00:50
keuanganku-queue:keuanganku-queue_01   RUNNING   pid 1235, uptime 0:00:50
```

### Step 4: Useful Supervisor Commands

```bash
# Start all programs
sudo supervisorctl start all

# Stop all programs
sudo supervisorctl stop all

# Restart program
sudo supervisorctl restart keuanganku-queue

# View logs
tail -f /var/log/keuanganku-queue.log
tail -f /var/log/keuanganku-queue-error.log

# Interactive shell
sudo supervisorctl
```

### Step 5: Reload Supervisor Config After Deploy

Setelah deploy code baru, jalankan:

```bash
# Option 1: Graceful reload (recommended)
php artisan queue:restart
sudo supervisorctl restart keuanganku-queue

# Option 2: Kill workers (akan auto-restart via supervisor)
sudo supervisorctl stop keuanganku-queue
# tunggu 2 detik
sudo supervisorctl start keuanganku-queue
```

---

## Bagian 3: Alternative Options

### Option 1: Cron + Artisan Command

Jika tidak bisa pakai Supervisor, gunakan cron job:

```bash
# Add to crontab
* * * * * php /path/to/keuanganku/artisan queue:work --once

# Atau process dengan timeout
*/5 * * * * timeout 300 php /path/to/keuanganku/artisan queue:work
```

**Kelemahan:** Kurang elegant, banyak process yang start/stop.

### Option 2: Laravel Horizon (Redis Only)

Jika pakai Redis queue, bisa pakai Horizon dashboard:

```bash
composer require laravel/horizon

php artisan horizon:install

# Run
php artisan horizon
```

**Keuntungan:** Dashboard monitoring built-in
**Kerugian:** Hanya untuk Redis, Horizon juga perlu di-monitor

### Option 3: AWS SQS / Google Cloud Tasks

Untuk serverless/cloud deployment:

```php
'sqs' => [
    'driver' => 'sqs',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'prefix' => env('SQS_PREFIX'),
    'queue' => env('SQS_QUEUE'),
    'region' => env('AWS_DEFAULT_REGION'),
],
```

**Keuntungan:** Managed, auto-scaling, reliable
**Kerugian:** Berbayar (tapi relatively murah)

---

## Bagian 4: Troubleshooting

### Issue 1: Worker Tidak Memproses Job

**Diagnosis:**

```bash
# Check jobs table
SELECT COUNT(*) FROM jobs;

# Check if worker running
ps aux | grep "queue:work"

# Check logs
tail -f /var/log/keuanganku-queue.log
```

**Solusi:**
1. Pastikan QUEUE_CONNECTION di .env benar
2. Jalankan `php artisan queue:work` manual untuk test
3. Check database connection
4. Pastikan migration sudah dijalankan

### Issue 2: Jobs Stuck in Queue

**Cause:** Worker crash atau timeout

**Diagnosis:**

```bash
# Check age of oldest job
SELECT MAX(created_at) FROM jobs;

# Check failed jobs
SELECT * FROM failed_jobs ORDER BY failed_at DESC;
```

**Solusi:**
1. Clear queue: `php artisan queue:flush`
2. Check logs untuk error message
3. Increase timeout jika job lama

### Issue 3: High Memory Usage

**Diagnosis:**

```bash
# Monitor memory
free -h

# Check which process
ps aux | grep queue
```

**Solusi:**
1. Set `memorymax` di Supervisor config
2. Reduce `numprocs` (worker count)
3. Profile code untuk memory leak
4. Restart worker periodically

### Issue 4: Failed Jobs Accumulating

**Diagnosis:**

```bash
SELECT COUNT(*) FROM failed_jobs;
SELECT * FROM failed_jobs WHERE connection = 'database' LIMIT 5;
```

**Solusi:**
1. Retry failed jobs: `php artisan queue:retry all`
2. Check log untuk error pattern
3. Fix issue di job code
4. Clear failed jobs: `php artisan queue:forget {id}`

---

## Best Practices

1. **Monitor logs regularly**
   - Setup log rotation
   - Analyze error patterns
   - Alert on critical errors

2. **Separate queue workers by priority**
   ```bash
   # Worker 1: High priority jobs
   php artisan queue:work --queue=high
   
   # Worker 2: Normal priority jobs
   php artisan queue:work --queue=default
   
   # Worker 3: Low priority jobs (batch processing)
   php artisan queue:work --queue=low
   ```

3. **Set realistic retry counts**
   - Network request: 5 retries
   - Email: 3 retries
   - Batch job: 1 retry

4. **Use exponential backoff**
   - Reduce API rate limiting issues
   - Give external services time to recover

5. **Monitor failed_jobs table**
   - Weekly check untuk patterns
   - Fix issues quickly
   - Learn dari failure patterns

---

## Next Steps

1. Test di local dengan: `php artisan queue:work`
2. Buat Supervisor config
3. Deploy ke production
4. Monitor dengan: `ps aux | grep queue`, `tail -f logs`, database queries

