# Setup Telegram Multi-User (Per-User Bot Token)

## Situasi Saat Ini ✅

Sistem **sudah mendukung multi-user dengan setting per-user**! Setiap user dapat mengatur notifikasi Telegram mereka sendiri.

### Yang Sudah Ada:
- Tabel `telegram_settings` dengan `user_id` unique ✅
- Model `TelegramSetting` dengan relasi ke `User` ✅
- Controller untuk edit/update/test per-user ✅
- View form untuk user input `chat_id` ✅

## Struktur Database (Saat Ini)

```
telegram_settings
├── id
├── user_id (unique) ← Satu user = satu setting
├── chat_id (user masukkan di form)
├── is_active (checkbox)
└── timestamps
```

## Dua Model Setup yang Dimungkinkan

### Model 1: Satu Bot Global (Saat Ini) ✅
```
.env
├── TELEGRAM_BOT_TOKEN=<token-global>

Setiap user:
├── Input chat_id mereka sendiri
├── Toggle aktif/non-aktif
└── Notifikasi dikirim via bot global ke chat_id mereka
```

**Kelebihan:**
- Admin setup sekali di .env
- User tinggal dapat chat_id
- Lebih simpel operasional

**Kekurangan:**
- Admin harus manage satu bot untuk semua user

---

### Model 2: Custom Bot Per User (BARU - Opsional)

Jika Anda ingin setiap user bisa punya bot mereka sendiri:

**Database Schema Baru:**
```
telegram_settings
├── id
├── user_id (unique)
├── chat_id
├── bot_token (BARU - user input sendiri)
├── is_active
└── timestamps
```

**Implementasi:**

#### 1. Migration (tambahan)
```bash
php artisan make:migration add_bot_token_to_telegram_settings
```

```php
// database/migrations/xxxx_add_bot_token_to_telegram_settings.php
Schema::table('telegram_settings', function (Blueprint $table) {
    $table->string('bot_token')->nullable()->after('chat_id');
});
```

#### 2. Update Model
```php
// app/Models/TelegramSetting.php
class TelegramSetting extends Model
{
    protected $fillable = [
        'user_id',
        'chat_id',
        'bot_token',  // BARU
        'is_active',
    ];

    protected $hidden = ['bot_token'];  // Jangan tampil di API
}
```

#### 3. Update TelegramService
```php
// app/Services/TelegramService.php
public function send(?string $chatId, string $message, ?string $botToken = null): bool
{
    // Gunakan bot_token user jika ada, fallback ke global
    $token = $botToken ?: config('telegram.bot_token');
    
    if (blank($chatId) || blank($token)) {
        return false;
    }
    
    try {
        $response = Http::timeout(10)->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        return $response->successful();
    } catch (\Throwable $e) {
        Log::error('Telegram error: '.$e->getMessage());
        return false;
    }
}

public function notifyUser(User $user, string $message): bool
{
    $setting = $user->telegramSetting;

    if (! $setting || ! $setting->is_active || blank($setting->chat_id)) {
        return false;
    }

    // Pass bot_token user jika ada
    return $this->send($setting->chat_id, $message, $setting->bot_token);
}
```

#### 4. Update View Form
```blade
<!-- resources/views/telegram/edit.blade.php -->
<div>
    <label class="block text-sm font-medium mb-1">Bot Token Telegram (Opsional)</label>
    <input type="password" name="bot_token" 
        value="{{ old('bot_token', $setting->bot_token ?? '') }}"
        placeholder="Biarkan kosong untuk menggunakan bot global"
        class="w-full rounded-lg border-gray-300 px-3 py-2 border">
    <p class="text-xs text-gray-500 mt-1">Jika dikosongkan, akan menggunakan bot default dari admin</p>
</div>

<div>
    <label class="block text-sm font-medium mb-1">Chat ID Telegram</label>
    <input type="text" name="chat_id" 
        value="{{ old('chat_id', $setting->chat_id ?? '') }}" 
        required 
        placeholder="Contoh: 123456789"
        class="w-full rounded-lg border-gray-300 px-3 py-2 border">
</div>
```

#### 5. Update Form Request
```php
// app/Http/Requests/UpdateTelegramSettingRequest.php
public function rules(): array
{
    return [
        'chat_id' => 'required|string|max:50',
        'bot_token' => 'nullable|string|max:255',  // BARU
        'is_active' => 'boolean',
    ];
}
```

## Rekomendasi Implementasi

### Untuk Produksi Sekarang:
**Gunakan Model 1 (Satu Bot Global)** ✅
- Lebih stabil
- Lebih mudah dikelola
- Sudah berfungsi di sistem ini

### Untuk Masa Depan:
- Tambahkan opsi Model 2 jika user menginginkan bot sendiri
- Implementasi step-by-step sesuai checklist di atas
- Update dokumentasi user

## Fitur Tambahan (Opsional)

### 1. Validasi Bot Token
```php
// Tambah di TelegramService
public function validateBotToken(string $botToken): bool
{
    try {
        $response = Http::timeout(5)->get("https://api.telegram.org/bot{$botToken}/getMe");
        return $response->successful();
    } catch (\Throwable $e) {
        return false;
    }
}
```

### 2. Encrypt Bot Token (Security)
```php
// app/Models/TelegramSetting.php
use Illuminate\Database\Eloquent\Casts\Encrypted;

protected $casts = [
    'bot_token' => Encrypted::class,
    'is_active' => 'boolean',
];
```

### 3. Log Bot Token Changes (Audit)
```php
// app/Models/TelegramSetting.php
protected static function booted(): void
{
    static::updated(function ($model) {
        if ($model->isDirty('bot_token')) {
            Log::info('Bot token changed by user '.$model->user_id);
        }
    });
}
```

## Testing

### Via Artisan Tinker
```bash
php artisan tinker

# Get user
$user = User::find(1);

# Update setting
$user->telegramSetting()->updateOrCreate([], [
    'chat_id' => '123456789',
    'bot_token' => 'your-bot-token-here',
    'is_active' => true,
]);

# Test send
app(TelegramService::class)->notifyUser($user, 'Test message');
```

## Summary

✅ **Sudah mendukung multi-user:** Setiap user bisa set chat_id sendiri  
⏳ **Opsional:** Custom bot token per user (lihat Model 2)  
🔒 **Security:** Jangan simpan token di client-side  
📝 **Dokumentasi:** User perlu tahu langkah setup di README
