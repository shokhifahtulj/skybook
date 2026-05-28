#!/bin/bash
# Railway Build Script for SkyBook Laravel + Vite

set -e

echo "=== SkyBook Railway Build Started ==="

# 1. Install PHP dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# 2. Install Node dependencies
echo "📦 Installing Node dependencies..."
npm install --legacy-peer-deps --no-audit --no-fund --prefer-offline

# 3. Build Vite assets
echo "🔨 Building Vite assets..."
npm run build

# 4. Generate Laravel caches
echo "⚙️  Generating Laravel caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Run migrations (with force flag for production)
if [ "$MIGRATE_ON_BUILD" = "true" ]; then
    echo "🗄️  Running database migrations..."
    php artisan migrate --force
fi

# 6. Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link || true

# 7. Optimize application
echo "⚡ Optimizing application..."
php artisan optimize

echo "=== Build Complete ✅ ==="
