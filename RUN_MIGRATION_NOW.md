# Run Migration Now!

The error you're seeing means the UUID migration hasn't been executed yet. Follow these steps:

## Quick Fix (Choose One)

### Option 1: Using Artisan (Recommended)
```bash
php artisan migrate
```

This will:
1. Add `uuid` column to wallets table
2. Generate UUIDs for all existing wallets
3. Make the column unique and NOT NULL
4. Update route binding to use UUIDs

### Option 2: Using the Automated Script
```bash
chmod +x EXECUTE_IDOR_FIX.sh
./EXECUTE_IDOR_FIX.sh
```

## What Happens

**Before Migration:**
- URLs use integer IDs: `/wallets/1`
- `getRouteKeyName()` returns `'id'` (fallback)
- Application works normally

**After Migration:**
- URLs use UUIDs: `/wallets/f47ac10b-58cc-4372-a567-0e02b2c3d479`
- `getRouteKeyName()` returns `'uuid'`
- IDOR vulnerability eliminated

## Verification

After running the migration:
```bash
# Check if UUIDs were created
php artisan tinker
>>> App\Models\Wallet::first()->uuid
# Should show a UUID like: "f47ac10b-58cc-4372-a567-0e02b2c3d479"
```

Then visit `/wallets` - URLs should now use UUIDs!

## Rollback (If Needed)
```bash
php artisan migrate:rollback
```

This will remove the UUID column and restore integer IDs.

---

**That's it!** The model is already configured to handle the transition safely.
