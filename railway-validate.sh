#!/bin/bash

# ═══════════════════════════════════════════════════════════════
# Railway Deployment Validation Script
# ═══════════════════════════════════════════════════════════════

RESET='\033[0m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'

PASS=0
FAIL=0

check_pass() {
  echo -e "${GREEN}✓ $1${RESET}"
  ((PASS++))
}

check_fail() {
  echo -e "${RED}✗ $1${RESET}"
  ((FAIL++))
}

echo -e "${BLUE}═══════════════════════════════════════════════════════════════${RESET}"
echo -e "${BLUE}  🔍 Railway Deployment Validation${RESET}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${RESET}"

# ═══════════════════════════════════════════════════════════════
# Check 1: Procfile
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[Check 1] Procfile Configuration${RESET}"

if [ -f "Procfile" ]; then
  PROCFILE=$(cat Procfile)
  
  if echo "$PROCFILE" | grep -q "php artisan migrate"; then
    check_pass "Procfile includes migration command"
  else
    check_fail "Procfile missing 'php artisan migrate' command"
  fi
  
  if echo "$PROCFILE" | grep -q 'php -S 0.0.0.0:${PORT}'; then
    check_pass "Procfile uses correct PORT variable"
  else
    check_fail "Procfile PORT variable incorrect (should be \${PORT}, not \${PORT:-8000})"
  fi
  
  if echo "$PROCFILE" | grep -q "sh -c"; then
    check_pass "Procfile uses shell wrapper"
  else
    check_fail "Procfile missing shell wrapper"
  fi
else
  check_fail "Procfile not found"
fi

# ═══════════════════════════════════════════════════════════════
# Check 2: nixpacks.toml
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[Check 2] nixpacks.toml Configuration${RESET}"

if [ -f "nixpacks.toml" ]; then
  NIXPACKS=$(cat nixpacks.toml)
  
  if echo "$NIXPACKS" | grep -q "procFile"; then
    check_fail "nixpacks.toml references 'procFile' (should be removed)"
  else
    check_pass "nixpacks.toml doesn't reference non-existent procFile"
  fi
  
  if echo "$NIXPACKS" | grep -q "php83Extensions.pdo_mysql"; then
    check_pass "nixpacks.toml includes pdo_mysql extension"
  else
    check_fail "nixpacks.toml missing pdo_mysql extension"
  fi
  
  if echo "$NIXPACKS" | grep -q "npm run build"; then
    check_pass "nixpacks.toml includes Vite build step"
  else
    check_fail "nixpacks.toml missing Vite build"
  fi
else
  check_fail "nixpacks.toml not found"
fi

# ═══════════════════════════════════════════════════════════════
# Check 3: .env Files
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[Check 3] Environment Configuration${RESET}"

if [ -f ".env" ]; then
  if grep -q "^APP_KEY=base64:" .env; then
    check_pass ".env has APP_KEY set"
  else
    check_fail ".env APP_KEY not set or invalid format"
  fi
  
  if grep -q "^APP_ENV=" .env; then
    check_pass ".env has APP_ENV configured"
  else
    check_fail ".env APP_ENV not configured"
  fi
fi

if [ -f ".env.production" ]; then
  if grep -q "^APP_KEY=base64:" .env.production; then
    check_pass ".env.production has APP_KEY"
  else
    check_fail ".env.production APP_KEY not set"
  fi
  
  if grep -q "^APP_ENV=production" .env.production; then
    check_pass ".env.production APP_ENV=production"
  else
    check_fail ".env.production APP_ENV not set to production"
  fi
  
  if grep -q "^DB_HOST=" .env.production; then
    check_pass ".env.production has DB_HOST"
  else
    check_fail ".env.production missing DB_HOST"
  fi
else
  check_fail ".env.production not found"
fi

# ═══════════════════════════════════════════════════════════════
# Check 4: Application Setup
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[Check 4] Application Setup${RESET}"

if [ -f "composer.json" ]; then
  check_pass "composer.json exists"
else
  check_fail "composer.json not found"
fi

if [ -f "package.json" ]; then
  check_pass "package.json exists"
else
  check_fail "package.json not found"
fi

if [ -d "vendor" ]; then
  check_pass "vendor directory exists"
else
  check_fail "vendor directory missing (run: composer install)"
fi

if [ -d "node_modules" ]; then
  check_pass "node_modules directory exists"
else
  check_fail "node_modules directory missing (run: npm install)"
fi

# ═══════════════════════════════════════════════════════════════
# Check 5: Database Setup
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[Check 5] Database Configuration${RESET}"

if [ -d "database/migrations" ]; then
  MIGRATION_COUNT=$(find database/migrations -type f -name "*.php" | wc -l)
  if [ "$MIGRATION_COUNT" -gt 0 ]; then
    check_pass "Database has $MIGRATION_COUNT migrations"
  else
    check_fail "No migrations found in database/migrations"
  fi
else
  check_fail "database/migrations directory not found"
fi

# ═══════════════════════════════════════════════════════════════
# Check 6: Frontend Assets
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[Check 6] Frontend Assets${RESET}"

if [ -d "public/build" ]; then
  if [ -f "public/build/manifest.json" ]; then
    check_pass "Vite manifest exists (public/build/manifest.json)"
  else
    check_fail "Vite manifest not built (run: npm run build)"
  fi
else
  check_fail "public/build directory missing (run: npm run build)"
fi

# ═══════════════════════════════════════════════════════════════
# Check 7: Git Configuration
# ═══════════════════════════════════════════════════════════════

echo -e "\n${YELLOW}[Check 7] Git Configuration${RESET}"

if [ -d ".git" ]; then
  check_pass ".git repository found"
  
  if git remote | grep -q "railway"; then
    check_pass "Railway remote configured"
  else
    check_fail "Railway remote not configured (run: railway link)"
  fi
else
  check_fail ".git repository not found"
fi

# ═══════════════════════════════════════════════════════════════
# Summary
# ═══════════════════════════════════════════════════════════════

echo -e "\n${BLUE}═══════════════════════════════════════════════════════════════${RESET}"
echo -e "Passed: ${GREEN}$PASS${RESET} | Failed: ${RED}$FAIL${RESET}"

if [ $FAIL -eq 0 ]; then
  echo -e "${GREEN}✅ All checks passed! Ready for deployment.${RESET}"
  echo -e "\n${YELLOW}Next steps:${RESET}"
  echo "1. Commit changes:"
  echo "   ${BLUE}git add . && git commit -m 'fix: Railway deployment configuration'${RESET}"
  echo ""
  echo "2. Push to Railway:"
  echo "   ${BLUE}git push railway main${RESET}"
  echo ""
  echo "3. Monitor deployment:"
  echo "   ${BLUE}railway logs --follow${RESET}"
  exit 0
else
  echo -e "${RED}❌ Fix $FAIL issue(s) before deploying.${RESET}"
  exit 1
fi
