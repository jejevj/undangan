#!/bin/bash

###############################################################################
# Payment Gateway Setup Script
# 
# This script will:
# 1. Run payment gateway seeders
# 2. Assign permissions to admin role
# 3. Clear caches
###############################################################################

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Payment Gateway Setup${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

# Step 1: Run seeders
echo -e "${YELLOW}[1/4] Running payment gateway seeders...${NC}"
php artisan db:seed --class=PaymentGatewayPermissionSeeder --force
php artisan db:seed --class=PaymentGatewayMenuSeeder --force

# Step 2: Assign permissions to admin role
echo -e "${YELLOW}[2/4] Assigning permissions to admin role...${NC}"
php artisan tinker --execute="
\$adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
if (\$adminRole) {
    \$adminRole->givePermissionTo([
        'payment-gateway.view',
        'payment-gateway.create',
        'payment-gateway.edit',
        'payment-gateway.delete'
    ]);
    echo 'Permissions assigned to admin role\n';
} else {
    echo 'Admin role not found!\n';
}
"

# Step 3: Clear caches
echo -e "${YELLOW}[3/4] Clearing caches...${NC}"
php artisan optimize:clear
php artisan permission:cache-reset

# Step 4: Verify
echo -e "${YELLOW}[4/4] Verifying setup...${NC}"
php artisan tinker --execute="
\$permissions = \Spatie\Permission\Models\Permission::where('name', 'like', 'payment-gateway%')->count();
\$menu = \App\Models\Menu::where('name', 'Payment Gateway')->first();
echo 'Permissions found: ' . \$permissions . '\n';
echo 'Menu found: ' . (\$menu ? 'Yes' : 'No') . '\n';
"

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Setup completed successfully!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Logout from dashboard"
echo "2. Clear browser cache (Ctrl+Shift+Delete)"
echo "3. Login again"
echo "4. Menu 'Payment Gateway' should now appear"
echo ""
