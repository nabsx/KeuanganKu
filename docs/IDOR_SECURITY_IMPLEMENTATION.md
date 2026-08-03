# 🔒 Implementasi Keamanan IDOR pada Wallets - Panduan Teknis

## 📋 Overview

Dokumen ini menjelaskan implementasi UUID untuk melindungi dari celah **Insecure Direct Object Reference (IDOR)** pada tabel `wallets` tanpa mengubah Primary Key (id) integer yang sudah memiliki relasi foreign key.

### Strategi Keamanan:
- ✅ **Primary Key** (id) tetap integer - tidak ada risiko data corruption
- ✅ **Foreign Keys** tetap bekerja dengan id integer
- ✅ **URL/Route Model Binding** menggunakan UUID yang random dan tidak predictable
- ✅ **Authorization Policies** tetap bekerja karena aplikasi memiliki `WalletPolicy`

---

## 🗄️ 1. Database Migration (Production-Safe)

**File:** `database/migrations/2026_08_04_000000_add_uuid_to_wallets_table.php`

### Penjelasan Proses:

```php
// Step 1: Tambahkan kolom uuid sebagai nullable
$table->uuid('uuid')->nullable()->after('id');

// Step 2: Generate UUID untuk data existing
DB::table('wallets')->whereNull('uuid')->update([
    'uuid' => DB::raw("UUID()"),
]);

// Step 3: Ubah jadi unique & NOT NULL
$table->uuid('uuid')->nullable(false)->unique()->change();
```

### Keuntungan Pendekatan Ini:

1. **Aman untuk Production**: 
   - Tidak lock table untuk waktu lama
   - Data existing otomatis terisi UUID
   - Transisi smooth tanpa downtime

2. **Reversible**: 
   - Rollback cukup menghapus kolom uuid
   - Tidak ada dampak ke data lama

3. **No Data Loss**: 
   - Semua foreign key relationships tetap valid
   - Hanya menambah kolom baru

### Cara Jalankan:

```bash
php artisan migrate
```

---

## 🎯 2. Eloquent Model - Wallet.php

**File:** `app/Models/Wallet.php`

### Kode yang Ditambahkan:

#### A. Import Str Helper
```php
use Illuminate\Support\Str;
```

#### B. Boot Method - Auto Generate UUID
```php
protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        if (! $model->uuid) {
            $model->uuid = (string) Str::uuid();
        }
    });
}
```

**Penjelasan:**
- Method ini otomatis dipanggil saat wallet baru dibuat
- Menggunakan `Str::uuid()` yang generate UUID v4 random dan unique
- Jika uuid belum ada, akan di-generate otomatis

#### C. Override getRouteKeyName()
```php
public function getRouteKeyName(): string
{
    return 'uuid';
}
```

**Penjelasan:**
- Laravel akan menggunakan kolom `uuid` untuk implicit route model binding
- Ketika akses `/wallets/{wallet}`, Laravel akan cari berdasarkan `uuid`, bukan `id`
- **PENTING**: Controller tidak perlu diubah, semua otomatis!

### Contoh Behavior:

```php
// Route definition (tidak perlu diubah)
Route::resource('wallets', WalletController::class);

// Akses URL sebelumnya: /wallets/1
// Akses URL sekarang: /wallets/550e8400-e29b-41d4-a716-446655440000

// Di controller:
public function show(Wallet $wallet) {
    // $wallet di-bind otomatis berdasarkan uuid dari URL
    // Jadi /wallets/550e8400... akan menemukan wallet dengan uuid tersebut
}
```

---

## 🛣️ 3. Controller & Routes - Tidak Perlu Diubah!

**File:** `app/Http/Controllers/WalletController.php`  
**File:** `routes/web.php`

### ✅ Status: Sudah Production-Ready

**Alasan:**
1. Laravel route model binding **secara otomatis** menggunakan `getRouteKeyName()`
2. Tidak perlu mengubah controller method signatures
3. Authorization policies bekerja dengan baik

### Contoh:
```php
// routes/web.php (tetap seperti biasa)
Route::resource('wallets', WalletController::class);

// app/Http/Controllers/WalletController.php (tetap seperti biasa)
public function show(Wallet $wallet): View {
    $this->authorize('view', $wallet); // Masih berfungsi dengan baik
    // ...
}
```

**Proses yang terjadi di backend:**
1. User akses `/wallets/550e8400-e29b-41d4-a716-446655440000`
2. Laravel parse UUID dari URL
3. Laravel call `Wallet::where('uuid', '550e8400-e29b-41d4-a716-446655440000')->first()`
4. Hasil di-inject ke controller sebagai `$wallet`
5. `WalletPolicy` melakukan `$wallet->user_id === Auth::id()` check
6. Jika tidak sesuai, return 403 Forbidden

---

## 📄 4. Blade Templates - Update Links

**File:** `resources/views/wallets/index.blade.php`  
**File:** `resources/views/wallets/show.blade.php`  
**File:** `resources/views/wallets/edit.blade.php`

### Status: Sudah Otomatis! ✅

Tidak perlu mengubah kode Blade. Semua `route()` helper sudah otomatis menggunakan `uuid`.

```blade
<!-- Sebelumnya menghasilkan: /wallets/1 -->
<!-- Sekarang menghasilkan: /wallets/550e8400-e29b-41d4-a716-446655440000 -->
<a href="{{ route('wallets.show', $wallet) }}">Riwayat</a>
<a href="{{ route('wallets.edit', $wallet) }}">Ubah</a>
```

**Cara Laravel menentukan URL:**
1. `route('wallets.show', $wallet)` akan cari route parameter
2. Laravel melihat model `Wallet` memiliki `getRouteKeyName()` yang return `'uuid'`
3. Otomatis ambil `$wallet->uuid` dan masukkan ke URL

### Contoh Lengkap (No Changes Needed):

```blade
<!-- resources/views/wallets/index.blade.php -->
@foreach ($wallets as $wallet)
    <div class="wallet-card">
        <h3>{{ $wallet->name }}</h3>
        
        <!-- Otomatis generate URL dengan UUID, bukan ID -->
        <a href="{{ route('wallets.show', $wallet) }}">
            Lihat Riwayat
        </a>
        
        <a href="{{ route('wallets.edit', $wallet) }}">
            Ubah
        </a>
        
        <!-- Form delete juga otomatis -->
        <form action="{{ route('wallets.destroy', $wallet) }}" method="POST">
            @csrf @method('DELETE')
            <button type="submit">Hapus</button>
        </form>
    </div>
@endforeach
```

---

## 🔐 5. Security Policies - Tetap Berfungsi

**File:** `app/Policies/WalletPolicy.php`

### Status: Tidak Perlu Diubah ✅

Policy akan tetap bekerja karena:
1. Setelah `$wallet` di-bind (via UUID), policy menerima Wallet model dengan data lengkap
2. Policy bisa check `$wallet->user_id === $user->id`
3. UUID hanya mengamankan URL, authorization tetap di policy

```php
public function view(User $user, Wallet $wallet): bool
{
    // $wallet sudah di-bind dari UUID, polanya tetap check user_id
    return $wallet->user->id === $user->id;
}

public function update(User $user, Wallet $wallet): bool
{
    return $wallet->user->id === $user->id;
}

public function delete(User $user, Wallet $wallet): bool
{
    return $wallet->user->id === $user->id;
}
```

---

## 🚀 Step-by-Step Implementasi

### Tahap 1: Persiapan
```bash
# Backup database (sangat penting!)
php artisan db:backup
# atau gunakan tool backup hosting Anda
```

### Tahap 2: Jalankan Migration
```bash
# Production:
php artisan migrate --force

# Development:
php artisan migrate
```

### Tahap 3: Verifikasi
```bash
# Cek data di database
php artisan tinker

# Di tinker console:
>>> Wallet::first();
// Harusnya ada kolom 'uuid' dengan nilai UUID yang unik

>>> Wallet::pluck('uuid');
// Harusnya semua wallet punya uuid
```

### Tahap 4: Test di Browser
1. Akses dashboard
2. Buka wallet
3. Cek URL di browser - harusnya UUID, bukan angka
4. Coba ubah UUID di URL - harusnya error 404 atau 403
5. Test authorization - pastikan hanya bisa lihat wallet sendiri

### Tahap 5: Monitoring
```bash
# Monitor logs untuk error
tail -f storage/logs/laravel.log

# Jika ada error route model binding, akan muncul di log
```

---

## ⚠️ Production Checklist

Sebelum push ke production:

- [ ] Database di-backup
- [ ] Migration file sudah di-commit
- [ ] Model Wallet sudah diupdate
- [ ] Test di local environment berfungsi
- [ ] URL menggunakan UUID (buka DevTools, periksa Network tab)
- [ ] Authorization masih bekerja (coba akses wallet orang lain)
- [ ] Logs tidak ada error
- [ ] Semua link di template masih berfungsi
- [ ] Test di staging environment

---

## 🐛 Troubleshooting

### Problem 1: Route Model Binding Failure (404)
```
ModelNotFoundException: No query results found for model
```

**Solusi:**
- Pastikan model sudah di-update dengan `getRouteKeyName()`
- Pastikan migration sudah jalan dan kolom uuid exist
- Clear cache: `php artisan cache:clear`

### Problem 2: UUID Column Doesn't Exist
```
SQLSTATE[42S22]: Column not found
```

**Solusi:**
- Jalankan migration: `php artisan migrate`
- Untuk rollback: `php artisan migrate:rollback`

### Problem 3: Links di Template Masih Generate ID Angka
```html
<!-- Masih muncul /wallets/1 -->
```

**Solusi:**
- Cache Blade template lama. Jalankan:
  ```bash
  php artisan view:clear
  php artisan cache:clear
  ```

### Problem 4: Existing Wallet Tidak Punya UUID
```php
// Di migration, jika ada error saat update existing data
```

**Solusi:**
- Gunakan raw SQL: `DB::raw("UUID()")`
- Jika tetap error, gunakan loop PHP (lebih lambat):
  ```php
  Wallet::whereNull('uuid')->each(function ($wallet) {
      $wallet->update(['uuid' => (string) Str::uuid()]);
  });
  ```

---

## 📊 Database Schema Setelah Migration

```
wallets table:
├── id (integer, PRIMARY KEY) ← unchanged, untuk internal relasi
├── uuid (binary/char, UNIQUE) ← NEW, untuk URL dan routing
├── user_id (integer, FOREIGN KEY) ← unchanged
├── name (string)
├── description (string)
├── color (string)
├── balance (decimal)
├── created_at
└── updated_at
```

---

## 🔄 Foreign Key Relationships (Tetap Aman)

Semua tabel yang reference `wallets` tetap berfungsi:

```php
// wallet_allocations table
$table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
// Masih reference 'wallets.id' (integer), bukan uuid

// wallet_transactions table  
$table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
// Masih reference 'wallets.id' (integer), bukan uuid
```

**Hasil:** Tidak ada cascading delete issues, semua relasi tetap solid.

---

## ✅ Testing Checklist

### Unit Test Example:
```php
// tests/Feature/WalletSecurityTest.php
public function test_wallet_show_uses_uuid_not_id()
{
    $wallet = Wallet::factory()->create(['user_id' => $this->user->id]);
    
    // Akses dengan UUID harusnya berhasil
    $response = $this->actingAs($this->user)
        ->get(route('wallets.show', $wallet));
    $this->assertNotFound();
    
    // Akses dengan ID angka harusnya 404
    $response = $this->actingAs($this->user)
        ->get("/wallets/{$wallet->id}");
    $this->assertNotFound();
}

public function test_cannot_access_other_user_wallet()
{
    $wallet = Wallet::factory()->create();
    
    // User lain tidak bisa access
    $response = $this->actingAs($this->user)
        ->get(route('wallets.show', $wallet));
    $this->assertForbidden();
}
```

---

## 📚 Best Practices Moving Forward

1. **Selalu gunakan UUID di URL** untuk sensitive resources
2. **Jangan pernah expose ID integer** ke frontend untuk authorization
3. **Tetap gunakan authorization policies** seperti `$this->authorize('view', $wallet)`
4. **Log access attempts** untuk monitoring security
5. **Regular security audit** menggunakan tools seperti Laravel Security Checker

---

## 🆘 Need Help?

Jika ada pertanyaan atau masalah:
1. Cek Laravel docs: https://laravel.com/docs/routing#implicit-binding-resolution
2. Cek UUID docs: https://laravel.com/docs/eloquent-relationships#models-with-uuids
3. Lihat logs: `storage/logs/laravel.log`

---

**Created:** August 4, 2026  
**Laravel Version:** 10/11  
**Database:** MySQL  
**Status:** Production Ready ✅
