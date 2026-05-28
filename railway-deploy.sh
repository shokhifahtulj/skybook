#!/bin/bash

# ═══════════════════════════════════════════════════════════════
# Railway Deployment Script for SkyBook
# ═══════════════════════════════════════════════════════════════

set -e

RESET='\033[0m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'

echo -e "${BLUE}═══════════════════════════════════════════════════════════════${RESET}"
echo -e "${BLUE}  🚀 SkyBook Railway Deployment Script${RESET}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${RESET}"

# ═══════════════════════════════════════════════════════════════
# 1. CLEAR CACHE
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[1/8] Clearing caches...${RESET}"

php artisan config:clear && echo -e "${GREEN}✓ Config cleared${RESET}"
php artisan cache:clear && echo -e "${GREEN}✓ Cache cleared${RESET}"
php artisan route:clear && echo -e "${GREEN}✓ Routes cleared${RESET}"
php artisan view:clear && echo -e "${GREEN}✓ Views cleared${RESET}"

# ═══════════════════════════════════════════════════════════════
# 2. VERIFY APP_KEY
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[2/8] Verifying APP_KEY...${RESET}"

if [ -z "$APP_KEY" ]; then
  echo -e "${RED}✗ APP_KEY is empty!${RESET}"
  echo -e "${YELLOW}Generating new APP_KEY...${RESET}"
  php artisan key:generate --force
  echo -e "${GREEN}✓ APP_KEY generated${RESET}"
  echo -e "${RED}⚠️  Add this to Railway Variables:${RESET}"
  grep APP_KEY .env || true
else
  echo -e "${GREEN}✓ APP_KEY already set${RESET}"
fi

# ═══════════════════════════════════════════════════════════════
# 3. VERIFY DATABASE CONNECTION
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[3/8] Verifying database connection...${RESET}"

php artisan tinker --execute '
try {
    DB::connection()->getPdo();
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
' || {
  echo -e "${RED}✗ Database connection failed!${RESET}"
  echo "Check these variables:"
  echo "  DB_HOST: ${DB_HOST}"
  echo "  DB_PORT: ${DB_PORT}"
  echo "  DB_DATABASE: ${DB_DATABASE}"
  exit 1
}

# ═══════════════════════════════════════════════════════════════
# 4. DEPENDENCIES
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[4/8] Installing dependencies...${RESET}"

if [ ! -d "vendor" ]; then
  composer install --no-dev --optimize-autoloader --no-interaction && echo -e "${GREEN}✓ Composer dependencies installed${RESET}"
fi

if [ ! -d "node_modules" ]; then
  npm install --legacy-peer-deps --no-audit --no-fund && echo -e "${GREEN}✓ NPM dependencies installed${RESET}"
fi

# ═══════════════════════════════════════════════════════════════
# 5. BUILD ASSETS
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[5/8] Building frontend assets...${RESET}"

npm run build && echo -e "${GREEN}✓ Vite build successful${RESET}" || {
  echo -e "${RED}✗ Vite build failed${RESET}"
  exit 1
}

# ═══════════════════════════════════════════════════════════════
# 6. CACHE FOR PRODUCTION
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[6/8] Caching for production...${RESET}"

php artisan config:cache && echo -e "${GREEN}✓ Config cached${RESET}"
php artisan route:cache && echo -e "${GREEN}✓ Routes cached${RESET}"
php artisan view:cache && echo -e "${GREEN}✓ Views cached${RESET}"
php artisan optimize && echo -e "${GREEN}✓ Application optimized${RESET}"

# ═══════════════════════════════════════════════════════════════
# 7. STORAGE SETUP
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[7/8] Setting up storage...${RESET}"

php artisan storage:link 2>/dev/null || true && echo -e "${GREEN}✓ Storage link created${RESET}"

# ═══════════════════════════════════════════════════════════════
# 8. DATABASE MIGRATIONS
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[8/8] Running database migrations...${RESET}"

php artisan migrate --force && echo -e "${GREEN}✓ Migrations completed${RESET}" || {
  echo -e "${RED}✗ Migrations failed!${RESET}"
  php artisan migrate:status
  exit 1
}

# ═══════════════════════════════════════════════════════════════
# SUMMARY
# ═══════════════════════════════════════════════════════════════

echo -e "\n${BLUE}═══════════════════════════════════════════════════════════════${RESET}"
echo -e "${GREEN}✅ Deployment preparation complete!${RESET}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${RESET}"

echo -e "\n${YELLOW}Next steps:${RESET}"
echo "1. Verify PORT environment variable in Railway:"
echo "   ${BLUE}railway variables${RESET}"
echo ""
echo "2. Check logs during deployment:"
echo "   ${BLUE}railway logs --follow${RESET}"
echo ""
echo "3. Verify health check after deployment:"
echo "   ${BLUE}curl https://skybook.railway.app/up${RESET}"
echo ""

exit 0
