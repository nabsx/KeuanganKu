# Laravel Queue Implementation for KeuanganKu

## Overview

Panduan lengkap implementasi Laravel Queue untuk mengirim notifikasi Telegram secara asynchronous. Ini memastikan HTTP request ke Telegram API tidak memblok response ke user.

---

## Quick Navigation

### For Different Roles:

| Role | Start Here | Time | Goal |
|------|-----------|------|------|
| **Developer** | `docs/JOB_CREATION_GUIDE.md` | 1-2 hours | Understand job creation & implementation |
| **DevOps/Operations** | `docs/QUEUE_WORKER_SETUP.md` | 30 mins | Setup Supervisor, monitoring, deployment |
| **QA/Testing** | `tests/Feature/QueueTest.php` | 45 mins | Test suite & verification |
| **Project Manager** | `QUEUE_QUICK_START.md` | 15 mins | Understand changes & benefits |
| **Anyone** | `QUEUE_QUICK_START.md` → `docs/QUEUE_IMPLEMENTATION_GUIDE.md` | Progressive | Complete understanding |

---

## Documentation Files

### 1. `QUEUE_QUICK_START.md` - START HERE
- **Best for:** Everyone, quick understanding
- **Contents:**
  - 5-step setup instructions
  - Code changes explained
  - Command reference
  - FAQ & troubleshooting
  - Time: 15-20 minutes

### 2. `docs/QUEUE_IMPLEMENTATION_GUIDE.md`
- **Best for:** Developers, complete understanding
- **Contents:**
  - Database vs Redis comparison
  - Queue configuration (.env, config/queue.php)
  - Environment variables setup
  - Best practices
  - Time: 30-45 minutes

### 3. `docs/JOB_CREATION_GUIDE.md`
- **Best for:** Developers, job understanding
- **Contents:**
  - What is Job
  - Job structure breakdown
  - Constructor, handle(), backoff(), failed()
  - Job lifecycle
  - Dispatch methods (sync, delay, batch, chain)
  - Time: 1 hour

### 4. `docs/QUEUE_WORKER_SETUP.md`
- **Best for:** DevOps, production setup
- **Contents:**
  - Local development (php artisan queue:work)
  - Supervisor production setup
  - Alternative options (Horizon, SQS, Cron)
  - Monitoring & troubleshooting
  - Time: 1-2 hours

### 5. `config/supervisor/keuanganku-queue.conf`
- **Best for:** DevOps production deployment
- **Contents:**
  - Ready-to-use Supervisor config
  - Detailed comments
  - Troubleshooting tips
  - Time: 15 minutes (copy & customize)

### 6. `tests/Feature/QueueTest.php`
- **Best for:** QA, developers, testing
- **Contents:**
  - 12 comprehensive test cases
  - Queue::fake() examples
  - Job dispatch verification
  - Integration tests
  - Retry logic tests
  - Time: 1 hour to understand

### 7. `app/Jobs/SendTelegramNotification.php`
- **Best for:** Developers, implementation
- **Contents:**
  - Complete job implementation
  - All features: retry, timeout, backoff, failed()
  - Inline documentation
  - Time: 30 minutes to understand

### 8. `app/Http/Controllers/IncomeController.php` (Modified)
- **Best for:** Developers, implementation
- **Contents:**
  - Before/after code changes
  - Job dispatch examples
  - Time: 10 minutes to review

---

## Key Concepts

### What Changed?

**Notification Process:**
- **Before:** Synchronous - user waits for Telegram HTTP request to complete
- **After:** Asynchronous - user gets response immediately, Telegram sent in background

**User Experience:**
- **Before:** Page load time + Telegram API time (slow)
- **After:** Page load time only (fast)

### How It Works

```
1. User submits income form
   ↓
2. IncomeController validates & stores income
   ↓
3. Controller dispatch SendTelegramNotification job
   ↓
4. Job serialized & stored in `jobs` table
   ↓
5. HTTP 302 redirect response sent (INSTANT)
   ↓
6. Queue worker picks up job from `jobs` table (background)
   ↓
7. Job handle() method sends HTTP request to Telegram
   ↓
8. User receives notification (few seconds later, user doesn't notice)
```

### Database Schema

**jobs table:**
- Stores queued jobs
- Columns: id, queue, payload, attempts, reserved_at, available_at, created_at

**failed_jobs table:**
- Stores jobs that failed after all retries
- Useful for debugging & retry

**job_batches table:**
- For batch job tracking (optional, used for Bus::batch())

---

## Setup Summary

### Files Modified/Created:

```
NEW:
  ✓ app/Jobs/SendTelegramNotification.php
  ✓ docs/QUEUE_IMPLEMENTATION_GUIDE.md
  ✓ docs/JOB_CREATION_GUIDE.md
  ✓ docs/QUEUE_WORKER_SETUP.md
  ✓ docs/QUEUE_README.md (this file)
  ✓ config/supervisor/keuanganku-queue.conf
  ✓ tests/Feature/QueueTest.php
  ✓ QUEUE_QUICK_START.md

UPDATED:
  ✓ app/Http/Controllers/IncomeController.php
  ✓ Database migrations (already exist)
```

### Configuration:

```env
QUEUE_CONNECTION=database
```

### Commands Run:

```bash
php artisan migrate  # Create jobs, failed_jobs tables
php artisan queue:work  # Run worker (development)
php artisan test tests/Feature/QueueTest.php  # Run tests
```

---

## Best Practices

1. **Keep jobs simple** - One job = one responsibility
2. **Set realistic retry counts** - 3 for most cases, 5 for network calls
3. **Use backoff strategy** - Avoid rate limiting
4. **Log everything** - For debugging
5. **Monitor regularly** - Check failed_jobs table weekly
6. **Use meaningful queue names** - For prioritization
7. **Avoid large payloads** - Keep serialized data small
8. **Handle exceptions gracefully** - Implement failed() method

---

## Common Tasks

### Local Development

```bash
# Run queue worker
php artisan queue:work --verbose

# Test with one job
php artisan queue:work --once

# Specific queue
php artisan queue:work --queue=telegram
```

### Monitoring

```bash
# Check job count
DB::table('jobs')->count();

# Check failed jobs
php artisan queue:failed

# View logs
tail -f /var/log/keuanganku-queue.log
```

### Troubleshooting

```bash
# Clear all jobs
php artisan queue:flush

# Retry failed jobs
php artisan queue:retry all

# Restart gracefully
php artisan queue:restart
```

---

## Testing

All test cases included in `tests/Feature/QueueTest.php`:

```bash
# Run tests
php artisan test tests/Feature/QueueTest.php

# Run specific test
php artisan test tests/Feature/QueueTest.php --filter=test_send_telegram_notification_job_is_dispatched

# With coverage
php artisan test --coverage tests/Feature/QueueTest.php
```

---

## Production Deployment

### Option 1: Supervisor (Recommended)

```bash
# Copy config
sudo cp config/supervisor/keuanganku-queue.conf /etc/supervisor/conf.d/

# Register
sudo supervisorctl reread
sudo supervisorctl update

# Start
sudo supervisorctl start keuanganku-queue

# Check status
sudo supervisorctl status
```

### Option 2: Cron Job

```bash
# Add to crontab
* * * * * php /path/to/artisan queue:work --once
```

### Option 3: Cloud Queue (SQS, Google Cloud Tasks)

Update `QUEUE_CONNECTION` in `.env` and config/queue.php.

---

## Troubleshooting Checklist

- [ ] `php artisan migrate` ran successfully
- [ ] `QUEUE_CONNECTION=database` in `.env`
- [ ] `php artisan queue:work` runs without error
- [ ] Jobs appear in database when created
- [ ] Worker processes jobs successfully
- [ ] Tests pass: `php artisan test tests/Feature/QueueTest.php`

---

## FAQ

**Q: Do I need to run php artisan queue:work forever?**
A: Yes, in production. Use Supervisor to auto-restart. In development, run in separate terminal.

**Q: What if Telegram API is down?**
A: Job retries 3 times with backoff [1s, 5s, 10s]. Then moved to failed_jobs for manual retry.

**Q: Can I see job details?**
A: Yes, check `jobs` table or use `php artisan queue:failed` for failed jobs.

**Q: Does this work with Redis?**
A: Yes, change `QUEUE_CONNECTION=redis` in .env. No code changes needed.

**Q: How long does job take?**
A: Usually instant to few seconds. Telegram HTTP timeout is 30 seconds max.

**Q: What if job size is too large?**
A: Redis has size limits. For database queue, no hard limit but keep payloads small.

---

## Support Resources

- Laravel Queue Documentation: https://laravel.com/docs/queue
- Redis Configuration: https://laravel.com/docs/redis
- Supervisor Documentation: http://supervisord.org/
- Troubleshooting Guide: Check `docs/QUEUE_WORKER_SETUP.md`

---

## Next Steps

1. **Read:** `QUEUE_QUICK_START.md` (15 mins)
2. **Setup:** Follow 5-step setup in Quick Start
3. **Test:** Run `php artisan queue:work --verbose` locally
4. **Verify:** Create income & check job processing
5. **Deploy:** Use `config/supervisor/keuanganku-queue.conf` for production

---

## Document Version

- Version: 1.0
- Last Updated: August 4, 2024
- Status: Ready for Production

