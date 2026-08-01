# Setup Telegram Multi-User (Per User Bot Token & Chat ID)

## Overview

Sistem ini mendukung **dua model** setup Telegram:

### Model 1: Bot Global (Default) ✅
- Admin setup satu bot global di `.env` 
- Semua user menggunakan bot yang sama
- User hanya input Chat ID mereka sendiri
- Paling sederhana dan direkomendasikan untuk kebanyakan kasus

### Model 2: Bot Personal (Optional) ✅
- Setiap user bisa upload bot token pribadi mereka
- Jika user tidak upload, fallback ke bot global
- User input Chat ID + Bot Token sendiri
- Lebih fleksibel untuk use case khusus

---

## Setup Awal (Admin)

### 1. Setup Bot Global di `.env`

```bash
# .env
TELEGRAM_BOT_TOKEN=your_bot_token_from_botfather
```

**Cara mendapat bot token:**
1. Buka Telegram, cari `@BotFather`
2. Kirim perintah `/newbot`
3. BotFather akan meminta nama bot (contoh: KeuanganKuBot)
4. BotFather akan meminta username bot (contoh: keuanganku_bot)
5. BotFather memberikan token, copy-paste ke `.env`

```bash
# Contoh:
TELEGRAM_BOT_TOKEN=1234567890:ABCDEfghIJKlmnoPQRSTUVwxyz-1234567890
```

### 2. Run Database Migration

```bash
php artisan migrate
```

Ini akan:
- Membuat tabel `telegram_settings` (jika belum ada)
- Menambah kolom `bot_token` untuk optional bot pribadi user

---

## User Setup (Setiap User Lakukan Ini)

### A. Menggunakan Bot Global (Cara Paling Mudah)

1. **Login** ke aplikasi
2. Buka menu **Pengaturan** → **Notifikasi Telegram**
3. Masukkan **Chat ID Anda**:
   - Mulai chat dengan bot (klik Start / kirim pesan ke bot)
   - Buka di browser: `https://api.telegram.org/bot{TOKEN}/getUpdates`
   - Ganti `{TOKEN}` dengan token dari admin
   - Cari di hasilnya: `"chat":{"id": 123456789}`
   - Copy angka itu sebagai Chat ID
4. **Jangan isi Bot Token** (kosongkan saja)
5. Centang **"Aktifkan notifikasi Telegram"**
6. Klik **"Simpan Pengaturan"**
7. Klik **"Kirim pesan uji coba"** untuk verifikasi

✅ Selesai! Notifikasi akan dikirim ke chat Anda via bot global.

---

### B. Menggunakan Bot Pribadi Anda

Jika Anda ingin menggunakan bot Anda sendiri:

1. **Buat bot pribadi:**
   - Buka Telegram, cari `@BotFather`
   - Kirim `/newbot`
   - Ikuti prosesnya sampai mendapat token

2. **Login** ke aplikasi, buka **Pengaturan** → **Notifikasi Telegram**

3. **Masukkan data Anda:**
   - **Chat ID**: Sama seperti cara di atas (cari di `getUpdates`)
   - **Bot Token**: Paste token dari BotFather Anda
   - **Aktifkan Telegram**: Centang checkbox

4. **Klik Simpan**

5. **Kirim pesan uji coba** untuk verifikasi

✅ Selesai! Notifikasi akan dikirim dari bot Anda sendiri.

---

## Arsitektur Database

### Tabel: `telegram_settings`

```sql
telegram_settings
├── id (primary key)
├── user_id (unique, FK to users)
├── chat_id (string, user Telegram chat ID)
├── bot_token (text, optional bot token user)
├── is_active (boolean, enable/disable notifikasi)
├── created_at
└── updated_at
```

**Contoh data:**

| user_id | chat_id   | bot_token               | is_active |
|---------|-----------|-------------------------|-----------|
| 1       | 123456789 | NULL                    | 1         |
| 2       | 987654321 | 1234567890:ABCDEfghIJK... | 1         |
| 3       | 555444333 | NULL                    | 0         |

- User 1: Menggunakan bot global (bot_token kosong)
- User 2: Menggunakan bot pribadi (bot_token ada)
- User 3: Notifikasi dinonaktifkan

---

## Alur Pengiriman Notifikasi

### Ketika Transaksi Dicatat:

```
Controller (ManualTransactionController)
    ↓
$telegramService->notifyUser($user, $message)
    ↓
Cek setting user di database
    ├─ Jika is_active = false → STOP (tidak kirim)
    ├─ Jika chat_id kosong → STOP (tidak kirim)
    └─ Jika ada data...
        ↓
Tentukan bot token:
    ├─ Jika user punya bot_token → GUNAKAN bot_token user
    └─ Jika bot_token kosong → GUNAKAN TELEGRAM_BOT_TOKEN dari .env
        ↓
POST ke Telegram API
    ↓
Notifikasi sampai ke chat user
```

---

## Implementasi di Code

### 1. TelegramService - Sudah Diupdate

File: `app/Services/TelegramService.php`

```php
public function notifyUser(User $user, string $message): bool
{
    $setting = $user->telegramSetting;

    if (! $setting || ! $setting->is_active || blank($setting->chat_id)) {
        return false;
    }

    // Gunakan bot_token user jika ada, jika tidak gunakan default
    $botToken = $setting->bot_token ?: config('telegram.bot_token');

    return $this->send($setting->chat_id, $message, $botToken);
}
```

### 2. TelegramSetting Model - Sudah Diupdate

File: `app/Models/TelegramSetting.php`

```php
protected $fillable = [
    'user_id',
    'chat_id',
    'bot_token',  // ← Sudah ditambah
    'is_active',
];
```

### 3. Migration - Sudah Dibuat

File: `database/migrations/2025_01_15_000001_add_bot_token_to_telegram_settings.php`

```php
public function up(): void
{
    Schema::table('telegram_settings', function (Blueprint $table) {
        $table->text('bot_token')->nullable()->after('chat_id');
    });
}
```

### 4. Form View - Sudah Diupdate

File: `resources/views/telegram/edit.blade.php`

- Textarea untuk input Bot Token (opsional)
- Instruksi lengkap untuk 2 opsi setup
- Error messages terintegrasi

### 5. Request Validation - Sudah Diupdate

File: `app/Http/Requests/UpdateTelegramSettingRequest.php`

```php
public function rules(): array
{
    return [
        'chat_id' => ['required', 'string', 'max:50'],
        'bot_token' => ['nullable', 'string', 'max:500'],  // ← Sudah ditambah
        'is_active' => ['boolean'],
    ];
}
```

---

## Troubleshooting

### Notifikasi Tidak Terkirim?

1. **Cek aktivasi:**
   - Apakah checkbox "Aktifkan notifikasi Telegram" sudah dicentang?
   - Apakah setting sudah disimpan?

2. **Cek Chat ID:**
   - Apakah Chat ID sudah benar?
   - Test dengan "Kirim pesan uji coba"

3. **Cek Bot Token:**
   - Jika menggunakan bot pribadi, apakah token sudah benar?
   - Format harus: `<number>:<string>` (contoh: `123456789:ABCDEfghIJK`)

4. **Cek Permissions Bot:**
   - Apakah Anda sudah kirim `/start` ke bot?
   - Apakah bot sudah reply dengan sesuatu?

5. **Lihat Log:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Cari error `Telegram` untuk debugging

---

## .env Configuration

### Default (Wajib)

```env
# Bot token global (digunakan jika user tidak upload bot token pribadi)
TELEGRAM_BOT_TOKEN=your_bot_token_from_botfather
```

Contoh lengkap `.env`:
```env
APP_NAME=KeuanganKu
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://keuanganku.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=keuanganku
DB_USERNAME=root
DB_PASSWORD=secret

# Telegram Configuration
TELEGRAM_BOT_TOKEN=1234567890:ABCDEfghIJKlmnoPQRSTUVwxyz-1234567890
```

---

## Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│ Admin Setup (Satu Kali)                                      │
├─────────────────────────────────────────────────────────────┤
│ 1. Create Bot via @BotFather                                │
│ 2. Copy Bot Token                                            │
│ 3. Masukkan ke .env: TELEGRAM_BOT_TOKEN=<token>            │
│ 4. Run Migration: php artisan migrate                        │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│ User Setup (Setiap User)                                     │
├─────────────────────────────────────────────────────────────┤
│ 1. Login → Pengaturan → Notifikasi Telegram                 │
│ 2. Opsi A: Gunakan Bot Global                               │
│    - Input Chat ID saja                                     │
│    - Bot Token kosong                                       │
│ 3. Opsi B: Gunakan Bot Pribadi                              │
│    - Input Chat ID                                          │
│    - Input Bot Token pribadi                                │
│ 4. Centang "Aktifkan"                                       │
│ 5. Test dengan "Kirim pesan uji coba"                      │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│ Saat Transaksi Dicatat                                       │
├─────────────────────────────────────────────────────────────┤
│ 1. Controller trigger: $telegram->notifyUser($user, $msg)   │
│ 2. Cek setting user: is_active? chat_id? bot_token?        │
│ 3. Gunakan bot_token user atau fallback ke .env            │
│ 4. Kirim ke Telegram API                                    │
│ 5. Notifikasi sampai ke user ✅                             │
└─────────────────────────────────────────────────────────────┘
```

---

## File Checklist

✅ **Backend:**
- `database/migrations/2025_01_15_000001_add_bot_token_to_telegram_settings.php` - Migration
- `app/Models/TelegramSetting.php` - Model dengan bot_token
- `app/Http/Requests/UpdateTelegramSettingRequest.php` - Validasi form
- `app/Services/TelegramService.php` - Service dengan logic fallback

✅ **Frontend:**
- `resources/views/telegram/edit.blade.php` - Form 2 opsi setup

✅ **Config:**
- `config/telegram.php` - Config telegram
- `.env.example` - Contoh environment variables

✅ **Controller:**
- `app/Http/Controllers/ManualTransactionController.php` - Menggunakan notifikasi

---

## Catatan Penting

1. **Security**: Bot token adalah sensitive data, jangan commit ke git
2. **Fallback**: Sistem selalu fallback ke bot global jika bot pribadi gagal
3. **Error Handling**: Gagal kirim notifikasi tidak menghentikan proses transaksi
4. **Logging**: Semua error notifikasi dicatat di `storage/logs/laravel.log`

---

Selesai! Sistem multi-user telegram sudah siap digunakan. 🚀
