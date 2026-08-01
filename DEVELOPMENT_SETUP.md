# 🚀 Development Setup Guide - KeuanganKu

Panduan lengkap untuk menjalankan KeuanganKu di local environment Anda.

---

## 📋 Prerequisites

Pastikan Anda sudah install:

- **PHP 8.1+** (dengan extensions: curl, json, mbstring, sqlite, xml)
- **Composer** (dependency manager untuk PHP)
- **Node.js 16+** dan **npm** atau **yarn**
- **SQLite** atau **MySQL** untuk database

### Verifikasi Installation

```bash
# Check PHP version
php --version

# Check Composer
composer --version

# Check Node.js
node --version
npm --version
```

---

## 🔧 Initial Setup (First Time Only)

### 1. Install PHP Dependencies

```bash
cd /path/to/keuanganku
composer install
```

### 2. Setup Environment Variables

```bash
# Copy .env template
cp .env.example .env

# Generate APP_KEY
php artisan key:generate
```

### 3. Configure Database

Edit `.env`:

```env
# Option A: SQLite (Recommended for development)
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database/keuanganku.sqlite

# Option B: MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=keuanganku_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run Database Migrations

```bash
php artisan migrate
```

This will create all necessary tables including:
- `users` - User accounts
- `wallets` - Wallet data
- `wallet_transactions` - Transaction records
- `telegram_settings` - Telegram notification settings
- And more...

### 5. Install Frontend Dependencies

```bash
npm install
# or
yarn install
```

### 6. Build Frontend Assets

```bash
npm run build
# or for development with HMR
npm run dev
```

---

## 🏃 Running The Application

### Option 1: Using PHP Artisan Serve (Recommended)

**Terminal 1 - Start Laravel Backend:**
```bash
cd /path/to/keuanganku
php artisan serve
```

Output akan menunjukkan:
```
 INFO  Server running on [http://127.0.0.1:8000].
```

**Terminal 2 - Start Frontend Development (Optional but recommended)**
```bash
npm run dev
```

Ini untuk Hot Module Replacement (HMR) saat development.

### Option 2: Using PHP Built-in Server

```bash
php -S 127.0.0.1:8000 -t public
```

---

## 🌐 Access The Application

```
Landing Page:    http://127.0.0.1:8000
Login:           http://127.0.0.1:8000/login
Register:        http://127.0.0.1:8000/register
Dashboard:       http://127.0.0.1:8000/dashboard (auth required)
Wallets:         http://127.0.0.1:8000/wallets
Telegram:        http://127.0.0.1:8000/pengaturan/telegram
```

---

## 📝 Application Routes

### Public Routes (No auth required)

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/` | Landing page |
| GET | `/login` | Login page |
| POST | `/login` | Process login |
| GET | `/register` | Register page |
| POST | `/register` | Process registration |

### Protected Routes (Auth required)

| Method | Route | Description |
|--------|-------|-------------|
| POST | `/logout` | Logout user |
| GET | `/dashboard` | User dashboard |
| GET/POST | `/wallets` | Manage wallets |
| GET | `/wallets/{id}` | View wallet details |
| GET | `/wallets/{id}/transaksi-keluar` | Create out transaction |
| POST | `/wallets/{id}/transaksi-keluar` | Store out transaction |
| GET | `/pendapatan` | Income list |
| GET | `/pendapatan/tambah` | Create income |
| POST | `/pendapatan` | Store income |
| GET | `/persentase-wallet` | Wallet allocation settings |
| PUT | `/persentase-wallet` | Update allocation |
| GET | `/pengaturan/telegram` | Telegram settings |
| PUT | `/pengaturan/telegram` | Update Telegram settings |
| POST | `/pengaturan/telegram/test` | Test Telegram notification |

---

## 🗄️ Database Migrations

### Available Migrations

```bash
# List all migrations
php artisan migrate:status

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Run migrations with fresh database
php artisan migrate:fresh
```

### Creating Test Data

```bash
# Run seeders (if available)
php artisan db:seed
```

---

## 🔌 Telegram Configuration

### Setup Telegram Bot

1. **Create a new bot:**
   - Open Telegram, search `@BotFather`
   - Send `/newbot`
   - Follow prompts to create bot
   - Copy the bot token

2. **Get your Chat ID:**
   - Start chat with your bot (click Start)
   - Open: `https://api.telegram.org/bot<YOUR_TOKEN>/getUpdates`
   - Find your chat ID in the response

3. **Configure in .env:**
   ```env
   TELEGRAM_BOT_TOKEN=your_bot_token_here
   ```

4. **Setup in App:**
   - Login to KeuanganKu
   - Go to Pengaturan → Telegram
   - Enter your Chat ID
   - (Optional) Enter custom bot token if you want to use your own bot
   - Enable notifications
   - Click "Kirim Pesan Uji Coba"

---

## 🐛 Debugging

### View Logs

```bash
# View recent logs
tail -f storage/logs/laravel.log

# Clear logs
php artisan logs:clear
```

### Enable Debug Mode

Edit `.env`:
```env
APP_DEBUG=true
```

### Run Migrations in Verbose Mode

```bash
php artisan migrate --verbose
```

### Clear Cache

```bash
# Clear application cache
php artisan cache:clear

# Clear view cache
php artisan view:clear

# Clear config cache
php artisan config:clear

# Clear all caches
php artisan cache:clear && php artisan view:clear && php artisan config:clear
```

---

## 🧪 Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AuthTest.php

# Run with coverage
php artisan test --coverage
```

---

## 📦 Production Deployment

### Before Deploying

```bash
# Build production assets
npm run build

# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### Deploy Steps

1. Clone repository to production server
2. Run `composer install`
3. Run `npm install && npm run build`
4. Copy `.env.example` to `.env` and configure
5. Run `php artisan key:generate`
6. Run `php artisan migrate --force`
7. Set up proper directory permissions
8. Configure web server (Nginx/Apache)

---

## 🆘 Common Issues

### Issue: "No application encryption key has been specified"

**Solution:**
```bash
php artisan key:generate
```

### Issue: "SQLSTATE[HY000]: General error: 1 no such table"

**Solution:**
```bash
# Database migrations haven't been run
php artisan migrate
```

### Issue: "Class not found" errors

**Solution:**
```bash
# Regenerate autoloader
composer dump-autoload

# Clear cache
php artisan cache:clear
```

### Issue: Assets not loading (CSS/JS)

**Solution:**
```bash
# Rebuild assets
npm run dev
# or for production
npm run build

# Clear browser cache (Ctrl+Shift+Delete)
```

### Issue: Telegram notifications not working

**Solution:**
1. Verify `TELEGRAM_BOT_TOKEN` in `.env` is set correctly
2. Check Chat ID is correct in app settings
3. Make sure bot token is valid and bot is active
4. Check `storage/logs/laravel.log` for error details

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vite Documentation](https://vitejs.dev/)
- [Tailwind CSS](https://tailwindcss.com/)
- [KeuanganKu Landing Page](LANDING_PAGE.md)
- [Telegram Multi-User Setup](SETUP_TELEGRAM_MULTIUSER.md)
- [Manual Transaction Feature](FITUR_TRANSAKSI_KELUAR.md)

---

## 💡 Development Tips

### Use Laravel Tinker for Quick Testing

```bash
php artisan tinker

# Inside tinker:
>>> $user = User::first();
>>> $user->email;
>>> $user->wallets()->get();
```

### Generate Dummy Data

```bash
php artisan tinker
>>> \App\Models\User::factory(5)->create();
```

### Watch for Changes During Development

The `npm run dev` command automatically recompiles CSS/JS when files change.

### Database Reset During Development

```bash
# Reset database and re-run all migrations
php artisan migrate:fresh

# Reset and run seeders
php artisan migrate:fresh --seed
```

---

## ✅ Verification Checklist

After setup, verify:

- [ ] PHP version is 8.1+
- [ ] Composer installed and working
- [ ] Node.js and npm installed
- [ ] Database connection working
- [ ] All migrations completed successfully
- [ ] Frontend assets compiled
- [ ] Can access landing page at http://127.0.0.1:8000
- [ ] Can register and login
- [ ] Can create wallet
- [ ] Can record income
- [ ] Dashboard displays correctly

---

**Happy coding! 🎉**
