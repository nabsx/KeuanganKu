# Implementation Checklist

Quick checklist untuk implementasi Queue di KeuanganKu.

---

## Pre-Deployment Checklist

### Code Review
- [ ] Read: `QUEUE_QUICK_START.md`
- [ ] Reviewed: `app/Jobs/SendTelegramNotification.php`
- [ ] Reviewed: Updated `IncomeController.php`
- [ ] Reviewed: Test file `tests/Feature/QueueTest.php`
- [ ] Code approved by team

### Environment Setup
- [ ] `.env` contains: `QUEUE_CONNECTION=database`
- [ ] Database connection working
- [ ] Have database credentials
- [ ] Storage writable

### Database
- [ ] Migration exists: `database/migrations/0001_01_01_000002_create_jobs_table.php`
- [ ] Run: `php artisan migrate`
- [ ] Verified tables exist:
  - [ ] `jobs` table
  - [ ] `failed_jobs` table
  - [ ] `job_batches` table

### Testing
- [ ] Run: `php artisan test tests/Feature/QueueTest.php`
- [ ] All 12 tests pass
- [ ] No errors or warnings

### Local Development
- [ ] Terminal 1: `php artisan serve` works
- [ ] Terminal 2: `php artisan queue:work --verbose` works
- [ ] Created test income record
- [ ] Verified job in database: `SELECT * FROM jobs LIMIT 1;`
- [ ] Worker processed job successfully
- [ ] Check log: "Processed: App\Jobs\SendTelegramNotification"

---

## Staging Deployment

### Pre-Deploy
- [ ] All local checks passed
- [ ] Code committed to Git
- [ ] Created feature branch (if applicable)

### Deploy Steps
- [ ] Pull latest code to staging server
- [ ] Run: `composer install --no-dev`
- [ ] Run: `php artisan migrate`
- [ ] Run: `php artisan config:cache`
- [ ] Run: `php artisan route:cache`
- [ ] Clear cache: `php artisan cache:clear`

### Worker Setup
- [ ] Option A (Simple): `nohup php artisan queue:work &`
- [ ] Option B (Better): Setup Supervisor (see below)

### Verification
- [ ] Access staging application
- [ ] Create test income record
- [ ] Check: `php artisan queue:failed` (should be empty)
- [ ] Monitor logs: `tail -f /path/to/logs/laravel.log`
- [ ] Wait 1-2 minutes
- [ ] Verify Telegram notification received
- [ ] Database check: `SELECT COUNT(*) FROM jobs;` (should be 0)
- [ ] Database check: `SELECT COUNT(*) FROM failed_jobs;` (should be 0)

### Stress Testing (Optional)
- [ ] Create 10 income records quickly
- [ ] Monitor worker processing
- [ ] Check: `SELECT COUNT(*) FROM jobs;` (should decrease)
- [ ] Verify all Telegram notifications received
- [ ] No errors in logs

### 24-48 Hour Monitoring
- [ ] Monitor server resources (CPU, memory)
- [ ] Check logs daily
- [ ] Verify failed_jobs empty
- [ ] Performance acceptable
- [ ] No user complaints

---

## Production Deployment

### Pre-Deploy Review
- [ ] Staging tests completed successfully
- [ ] Business approval received
- [ ] Rollback plan prepared
- [ ] Team notified

### Production Setup: Supervisor Method (Recommended)

#### Step 1: Copy Config
```bash
sudo cp config/supervisor/keuanganku-queue.conf /etc/supervisor/conf.d/
```
- [ ] Confirmed: File copied to `/etc/supervisor/conf.d/`

#### Step 2: Edit Config
```bash
sudo nano /etc/supervisor/conf.d/keuanganku-queue.conf
```
- [ ] Changed: `/path/to/keuanganku` to actual project path
- [ ] Changed: `user=www-data` to actual server user
- [ ] Confirmed: `numprocs=2` (or appropriate for server)
- [ ] Confirmed: `memorymax=512` (or appropriate)
- [ ] Saved file

#### Step 3: Register with Supervisor
```bash
sudo supervisorctl reread
sudo supervisorctl update
```
- [ ] `reread` completed without error
- [ ] `update` completed without error

#### Step 4: Start Worker
```bash
sudo supervisorctl start keuanganku-queue
```
- [ ] Command executed
- [ ] No errors returned

#### Step 5: Verify Status
```bash
sudo supervisorctl status
```
- [ ] Output shows: `keuanganku-queue:keuanganku-queue_00 RUNNING`
- [ ] Output shows: `keuanganku-queue:keuanganku-queue_01 RUNNING` (if numprocs=2)

### Deployment Steps
- [ ] Pull latest code to production
- [ ] Run: `composer install --no-dev`
- [ ] Run: `php artisan migrate --force` (if needed)
- [ ] Run: `php artisan config:cache`
- [ ] Run: `php artisan route:cache`
- [ ] Run: `php artisan cache:clear`

### Post-Deploy Verification
- [ ] Application loads without error
- [ ] Create test income record
- [ ] Verify Telegram notification within 30 seconds
- [ ] Check: `sudo supervisorctl status` (workers RUNNING)
- [ ] Check logs: `tail -f /var/log/keuanganku-queue.log` (processing jobs)
- [ ] Database: `SELECT COUNT(*) FROM failed_jobs;` (should be 0)

### Monitoring (First 24 Hours)
- [ ] Every 1 hour: Check supervisor status
  ```bash
  sudo supervisorctl status
  ```
- [ ] Every 1 hour: Check for errors
  ```bash
  tail -n 20 /var/log/keuanganku-queue.log
  ```
- [ ] Every 4 hours: Database check
  ```bash
  mysql -u user -p database -e "SELECT COUNT(*) FROM failed_jobs;"
  ```
- [ ] Every 4 hours: Memory check
  ```bash
  free -h
  ps aux | grep queue
  ```

### Issues During Deployment?
If problems occur:
- [ ] Check logs: `tail -f /var/log/keuanganku-queue.log`
- [ ] Check supervisor config: `nano /etc/supervisor/conf.d/keuanganku-queue.conf`
- [ ] Restart: `sudo supervisorctl restart keuanganku-queue`
- [ ] Check database connection
- [ ] Verify QUEUE_CONNECTION in .env

---

## Post-Deployment Monitoring (Ongoing)

### Daily (First Week)
- [ ] Check supervisor status
- [ ] Check for failed jobs
- [ ] Review error logs
- [ ] Monitor performance

### Weekly (First Month)
- [ ] Database cleanup: `SELECT COUNT(*) FROM jobs;` (should be ~0)
- [ ] Review failed_jobs: `SELECT COUNT(*) FROM failed_jobs;`
- [ ] Check disk space used
- [ ] Review performance metrics

### Monthly (Ongoing)
- [ ] Archive old logs
- [ ] Clean up old job records (optional)
- [ ] Review worker performance
- [ ] Adjust settings if needed (retry count, timeout, workers)

### Useful Commands

**Check Status:**
```bash
sudo supervisorctl status
```

**Restart Worker:**
```bash
sudo supervisorctl restart keuanganku-queue
```

**View Logs:**
```bash
tail -f /var/log/keuanganku-queue.log
tail -f /var/log/keuanganku-queue-error.log
```

**Retry Failed Jobs:**
```bash
php artisan queue:retry all
```

**Flush Queue:**
```bash
php artisan queue:flush
```

**Database Status:**
```bash
php artisan tinker
> DB::table('jobs')->count()
> DB::table('failed_jobs')->count()
```

---

## Troubleshooting During Implementation

### Issue: "Class SendTelegramNotification not found"
- [ ] Run: `composer dump-autoload`
- [ ] Run: `php artisan clear-cache`
- [ ] Check: `app/Jobs/SendTelegramNotification.php` exists

### Issue: "QUEUE_CONNECTION configuration not found"
- [ ] Check: `.env` has `QUEUE_CONNECTION=database`
- [ ] Check: `config/queue.php` exists
- [ ] Run: `php artisan config:clear`

### Issue: Jobs not processed
- [ ] Check: `php artisan queue:work --once` (test single job)
- [ ] Check: Database connection
- [ ] Check: `jobs` table exists: `php artisan migrate`
- [ ] Check: Worker running: `ps aux | grep queue`

### Issue: High memory usage
- [ ] Reduce: `numprocs` in Supervisor config
- [ ] Check: Code for memory leaks
- [ ] Set: `memorymax=512` to auto-restart

### Issue: Failed jobs accumulating
- [ ] Check: `/var/log/keuanganku-queue.log` for errors
- [ ] Retry: `php artisan queue:retry all`
- [ ] Check: Telegram API connectivity
- [ ] Check: Bot token valid

---

## Rollback Plan (If Needed)

If major issues occur:

### Option 1: Disable Queue (Emergency)
```bash
# Stop workers
sudo supervisorctl stop keuanganku-queue

# Revert .env
QUEUE_CONNECTION=sync
```

This will make notifications synchronous (slow) but functional.

### Option 2: Rollback Code
```bash
git revert HEAD~N
composer install
php artisan migrate:rollback
```

### Option 3: Database Cleanup
```bash
php artisan queue:flush
php artisan queue:flush --failed
```

---

## Sign-Off

- [ ] Implementation lead: _______________  Date: _______
- [ ] QA/Testing lead: _______________  Date: _______
- [ ] Operations/DevOps: _______________  Date: _______
- [ ] Product/Manager: _______________  Date: _______

---

## Documentation References

- **Quick Start:** `QUEUE_QUICK_START.md`
- **Summary:** `QUEUE_IMPLEMENTATION_SUMMARY.md`
- **Full Guide:** `docs/QUEUE_README.md`
- **Configuration:** `docs/QUEUE_IMPLEMENTATION_GUIDE.md`
- **Job Details:** `docs/JOB_CREATION_GUIDE.md`
- **Worker Setup:** `docs/QUEUE_WORKER_SETUP.md`
- **Tests:** `tests/Feature/QueueTest.php`
- **Supervisor Config:** `config/supervisor/keuanganku-queue.conf`

---

## Notes

Use this space for implementation notes:

```
_____________________________________________________________________________

_____________________________________________________________________________

_____________________________________________________________________________

_____________________________________________________________________________

_____________________________________________________________________________
```

---

**Last Updated:** August 4, 2024
**Implementation Status:** Ready for Production

