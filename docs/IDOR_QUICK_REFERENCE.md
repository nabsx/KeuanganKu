# ⚡ IDOR Security - Quick Reference

## 🎯 Apa yang Berubah?

### URL Pattern
```
SEBELUM:  /wallets/1 (predictable, vulnerable to IDOR)
SESUDAH:  /wallets/550e8400-e29b-41d4-a716-446655440000 (UUID, random)
```

### Database Schema
```sql
-- BARU: Kolom uuid
ALTER TABLE wallets ADD COLUMN uuid BINARY(16) UNIQUE NOT NULL;
```

### Model Changes
```php
// 1. Import
use Illuminate\Support\Str;

// 2. Auto-generate UUID saat create
protected static function boot() {
    parent::boot();
    static::creating(function ($model) {
        if (!$model->uuid) {
            $model->uuid = (string) Str::uuid();
        }
    });
}

// 3. Tell Laravel to use uuid for routes
public function getRouteKeyName(): string {
    return 'uuid';
}
```

---

## 📋 Implementation Checklist

- [ ] Jalankan migration: `php artisan migrate`
- [ ] Update Wallet.php model
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Test di browser - URL harusnya UUID
- [ ] Test authorization - jangan bisa akses wallet orang lain
- [ ] Commit ke git & push ke production

---

## 🧪 Quick Test Commands

```bash
# Cek kolom uuid sudah ada
php artisan tinker
>>> Schema::hasColumn('wallets', 'uuid')

# Cek data sudah punya uuid
>>> Wallet::count()
>>> Wallet::whereNotNull('uuid')->count()

# Test route binding
>>> route('wallets.show', Wallet::first())
// Harusnya generate URL dengan uuid
```

---

## 🔍 Verification Steps

1. **Browser DevTools - Network Tab:**
   - Buka `/wallets` page
   - Klik salah satu wallet
   - Cek URL bar → harusnya `/wallets/[UUID]` bukan `/wallets/1`

2. **Test Authorization:**
   - Login dengan user A
   - User A punya wallet dengan UUID: `abc-123`
   - Copy UUID dari URL
   - Logout
   - Login dengan user B
   - Akses `/wallets/abc-123` → harusnya 403 Forbidden
   - **Kesimpulan:** IDOR sudah tertutup ✅

3. **Test Random UUID:**
   - Akses `/wallets/00000000-0000-0000-0000-000000000000` (UUID random)
   - Harusnya 404 Not Found
   - Bukan 403 (yang berarti authorized tapi data tidak ditemukan)

---

## ⚙️ Controller Code (No Changes!)

```php
class WalletController extends Controller {
    // Tetap sama - route binding otomatis use uuid
    public function show(Wallet $wallet): View {
        $this->authorize('view', $wallet);
        // ...
    }
    
    public function edit(Wallet $wallet): View {
        $this->authorize('update', $wallet);
        // ...
    }
}
```

---

## 🎨 Blade Template Code (No Changes!)

```blade
{{-- route() helper otomatis generate UUID --}}
<a href="{{ route('wallets.show', $wallet) }}">Lihat</a>
<a href="{{ route('wallets.edit', $wallet) }}">Ubah</a>

{{-- Form tetap sama --}}
<form action="{{ route('wallets.destroy', $wallet) }}" method="POST">
    @csrf @method('DELETE')
    <button>Hapus</button>
</form>
```

---

## 🔐 Authorization Policy (No Changes!)

```php
class WalletPolicy {
    public function view(User $user, Wallet $wallet): bool {
        return $wallet->user_id === $user->id;
    }
    
    public function update(User $user, Wallet $wallet): bool {
        return $wallet->user_id === $user->id;
    }
}
```

---

## 📊 Database Integrity (Safe!)

```
Foreign Keys tetap berfungsi:
- wallet_allocations.wallet_id → wallets.id ✅
- wallet_transactions.wallet_id → wallets.id ✅

Primary Key tetap:
- wallets.id → integer (tidak berubah) ✅

New Unique Column:
- wallets.uuid → binary (untuk routing) ✅
```

---

## 🚨 Common Issues & Fixes

| Issue | Solution |
|-------|----------|
| URL masih `/wallets/1` | Clear cache: `php artisan cache:clear` |
| 404 error di route model binding | Pastikan migration sudah jalan |
| UUID tidak ter-generate | Pastikan boot() method di model sudah ditambah |
| Links di Blade masih ID angka | Clear view cache: `php artisan view:clear` |

---

## 💡 Key Points

1. **ID integer tidak berubah** - aman untuk production
2. **UUID hanya di URL** - authorization masih di policy
3. **Otomatis di-generate** - tidak perlu manual input
4. **Reversible** - bisa rollback kapan saja
5. **Zero downtime** - migration aman untuk live database

---

## 📞 Support

Lihat file lengkap: `docs/IDOR_SECURITY_IMPLEMENTATION.md`
