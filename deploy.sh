#!/bin/bash

###############################################################################
# Laravel Deployment Script
# 
# Usage: ./deploy.sh [environment]
# Example: ./deploy.sh production
###############################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT=${1:-production}
APP_DIR=$(pwd)

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Laravel Deployment Script${NC}"
echo -e "${GREEN}Environment: ${ENVIRONMENT}${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

# Check if .env exists
if [ ! -f ".env" ]; then
    echo -e "${RED}Error: .env file not found!${NC}"
    echo -e "${YELLOW}Please copy .env.production.example to .env and configure it.${NC}"
    exit 1
fi

# Step 1: Enable Maintenance Mode
echo -e "${YELLOW}[1/10] Enabling maintenance mode...${NC}"
php artisan down --retry=60 || true

# Step 2: Pull latest code (if using git)
if [ -d ".git" ]; then
    echo -e "${YELLOW}[2/10] Pulling latest code from git...${NC}"
    git pull origin main || git pull origin master
else
    echo -e "${YELLOW}[2/10] Skipping git pull (not a git repository)${NC}"
fi

# Step 3: Install/Update Composer dependencies
echo -e "${YELLOW}[3/10] Installing composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction

# Step 4: Clear all caches
echo -e "${YELLOW}[4/10] Clearing all caches...${NC}"
php artisan optimize:clear

# Step 5: Run database migrations
echo -e "${YELLOW}[5/10] Running database migrations...${NC}"
php artisan migrate --force

# Step 6: Create storage link
echo -e "${YELLOW}[6/10] Creating storage symbolic link...${NC}"
php artisan storage:link || true

# Step 7: Cache configuration
echo -e "${YELLOW}[7/10] Caching configuration...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 8: Set proper permissions
echo -e "${YELLOW}[8/10] Setting file permissions...${NC}"
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true

# Step 9: Restart services
echo -e "${YELLOW}[9/10] Restarting services...${NC}"
if command -v systemctl &> /dev/null; then
    sudo systemctl restart php8.2-fpm || true
    sudo systemctl restart nginx || sudo systemctl restart apache2 || true
fi

# Step 10: Disable Maintenance Mode
echo -e "${YELLOW}[10/10] Disabling maintenance mode...${NC}"
php artisan up

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Deployment completed successfully!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${YELLOW}Post-deployment checklist:${NC}"
echo "1. Test the application: ${APP_URL}"
echo "2. Check error logs: tail -f storage/logs/laravel.log"
echo "3. Monitor server resources"
echo "4. Test critical features (login, payment, etc.)"
echo ""
