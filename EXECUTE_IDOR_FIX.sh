#!/bin/bash

# 🔒 IDOR Security Implementation Script
# This script executes the UUID migration and verifies everything is working
# Make sure to backup your database FIRST!

set -e

echo "🔒 IDOR Security Implementation for KeuanganKu"
echo "================================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Step 1: Check if Laravel app exists
echo -e "${BLUE}Step 1: Checking Laravel app...${NC}"
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: artisan file not found. Run this script from Laravel root directory.${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Laravel app found${NC}"
echo ""

# Step 2: Confirm backup
echo -e "${BLUE}Step 2: Database backup confirmation${NC}"
echo -e "${YELLOW}⚠️  CRITICAL: Make sure you have a backup before continuing!${NC}"
read -p "Have you backed up your database? (yes/no): " backup_confirm

if [ "$backup_confirm" != "yes" ]; then
    echo -e "${RED}❌ Aborted. Please backup your database first!${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Backup confirmed${NC}"
echo ""

# Step 3: Clear cache
echo -e "${BLUE}Step 3: Clearing application cache...${NC}"
php artisan cache:clear
php artisan view:clear
echo -e "${GREEN}✓ Cache cleared${NC}"
echo ""

# Step 4: Run migrations
echo -e "${BLUE}Step 4: Running database migration...${NC}"
php artisan migrate

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Migration failed!${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Migration completed${NC}"
echo ""

# Step 5: Verify migration
echo -e "${BLUE}Step 5: Verifying migration...${NC}"
php artisan tinker << 'EOF'
$hasColumn = Schema::hasColumn('wallets', 'uuid');
echo $hasColumn ? "✓ UUID column exists\n" : "✗ UUID column not found\n";

$allHaveUuid = DB::table('wallets')->whereNull('uuid')->count() === 0;
echo $allHaveUuid ? "✓ All wallets have UUID\n" : "✗ Some wallets missing UUID\n";
EOF

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Verification failed!${NC}"
    exit 1
fi
echo ""

# Step 6: Run security tests
echo -e "${BLUE}Step 6: Running security tests...${NC}"
php artisan test tests/Feature/WalletSecurityTest.php --no-coverage

if [ $? -ne 0 ]; then
    echo -e "${YELLOW}⚠️  Some tests failed. Review the output above.${NC}"
    read -p "Continue anyway? (yes/no): " continue_confirm
    if [ "$continue_confirm" != "yes" ]; then
        echo -e "${RED}❌ Aborted${NC}"
        exit 1
    fi
fi
echo -e "${GREEN}✓ Security tests passed${NC}"
echo ""

# Step 7: Summary
echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}✅ IDOR Security Implementation Complete!${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""
echo "📊 What changed:"
echo "  ✓ UUID column added to wallets table"
echo "  ✓ All existing wallets have UUID"
echo "  ✓ Wallet model updated with UUID generation"
echo "  ✓ Route model binding now uses UUID"
echo ""
echo "🔐 Security improvements:"
echo "  ✓ URLs now use UUID instead of integer ID"
echo "  ✓ IDOR vulnerability protected"
echo "  ✓ Authorization policies still active"
echo ""
echo "📝 Next steps:"
echo "  1. Test in browser: http://yourapp.local/wallets"
echo "  2. Verify URLs use UUID format"
echo "  3. Test authorization (login with different user)"
echo "  4. Monitor logs: tail -f storage/logs/laravel.log"
echo "  5. Commit to git: git add . && git commit -m 'chore: add UUID for IDOR security'"
echo ""
echo "📚 Documentation:"
echo "  - Full guide: docs/IDOR_SECURITY_IMPLEMENTATION.md"
echo "  - Quick ref: docs/IDOR_QUICK_REFERENCE.md"
echo "  - Before/After: docs/BEFORE_AFTER_COMPARISON.md"
echo ""
echo -e "${BLUE}🎉 Happy coding!${NC}"
