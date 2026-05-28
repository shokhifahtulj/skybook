# Railway Environment Variables for SkyBook

## Application Configuration

```env
APP_NAME=SkyBook
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_URL=https://skybook.railway.app
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
LOG_LEVEL=info
```

## Database (Railway MySQL)

```env
DB_CONNECTION=mysql
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=${DB_PASSWORD}
```

## Cache & Session (File-based for Free Tier)

```env
CACHE_STORE=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
QUEUE_CONNECTION=database
```

## Optional: Redis (Premium Tier)

If upgrading to paid tier with Redis:

```env
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=${REDIS_HOST}
REDIS_PASSWORD=${REDIS_PASSWORD}
REDIS_PORT=6379
```

Then update config/cache.php to use Redis driver.

---

## Setup Instructions

### Step 1: Create APP_KEY

Locally, generate a new key:
```bash
php artisan key:generate --show
```

Copy the entire `base64:...` output.

### Step 2: Add to Railway

In Railway Dashboard → Project Settings → Environment Variables:

1. Name: `APP_KEY`
   Value: `base64:...` (from above)

2. Name: `APP_NAME`
   Value: `SkyBook`

3. Name: `APP_ENV`
   Value: `production`

4. Name: `APP_DEBUG`
   Value: `false`

5. Name: `LOG_LEVEL`
   Value: `info`

### Step 3: Connect Database

In Railway Dashboard:
- Add MySQL service (if not already added)
- Link to SkyBook project
- Environment variables auto-populate:
  - `DB_HOST`
  - `DB_PASSWORD`
  - etc.

### Step 4: Configure URLs

```env
APP_URL=https://skybook.railway.app
MAIL_FROM_ADDRESS=noreply@skybook.app
```

### Step 5: Deploy

```bash
git push railway main
```

Railway will:
1. Read nixpacks.toml
2. Install dependencies
3. Build Vite assets
4. Cache configurations
5. Start PHP server on $PORT
6. Health check /up endpoint

---

## Testing Deployment

After deployment succeeds:

```bash
# 1. Check health
curl https://skybook.railway.app/up

# Expected: 200 OK with "pong"

# 2. Check homepage loads
curl https://skybook.railway.app

# Expected: 200 with HTML response

# 3. Check Vite assets
curl https://skybook.railway.app/build/manifest.json

# Expected: 200 with manifest JSON

# 4. Test API (if applicable)
curl https://skybook.railway.app/api/ping

# Expected: API response or 404 (if endpoint doesn't exist)
```

---

## Common Issues & Fixes

### Issue: "App crashed on startup"

**Diagnostics:**
```bash
railway logs --limit 50
```

**Likely causes:**
1. Missing `APP_KEY` → Set in Railway environment variables
2. Database not connected → Verify MySQL service linked
3. Missing migrations → Run `railway run php artisan migrate`
4. Cache files corrupted → Run `railway run php artisan cache:clear`

**Fix:**
```bash
railway run php artisan config:clear
railway run php artisan cache:clear
railway run php artisan migrate --force
railway redeploy
```

### Issue: "Vite manifest not found"

**Cause:** Assets weren't built

**Fix:**
```bash
npm run build
git add public/build
git commit -m "Add Vite build artifacts"
git push railway main
```

### Issue: "502 Bad Gateway"

**Cause:** PHP server not responding

**Check:**
```bash
# Is it listening?
railway shell
curl http://127.0.0.1:8000/up

# Check PORT variable
railway variables
```

### Issue: "Database connection error"

**Cause:** Credentials or host wrong

**Check:**
```bash
railway shell
php artisan tinker
# In Tinker:
# DB::connection()->getPdo();
```

---

## Production Checklist

Before pushing to production:

- [ ] Generate and set `APP_KEY`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure database credentials
- [ ] Set `LOG_LEVEL=info`
- [ ] Test locally: `npm run build && php -S 127.0.0.1:8000 -t public`
- [ ] Verify: `curl http://127.0.0.1:8000/up`
- [ ] Commit changes: `git add .`
- [ ] Push to Railway: `git push railway main`
- [ ] Monitor logs: `railway logs --follow`
- [ ] Verify health: `curl https://skybook.railway.app/up`
- [ ] Test features

---

## Monitoring & Maintenance

### View Logs

```bash
# Last 50 lines
railway logs --limit 50

# Follow in real-time
railway logs --follow

# Search for errors
railway logs | grep -i error
```

### Clear Caches

```bash
# Clear application cache
railway run php artisan cache:clear

# Clear configuration cache
railway run php artisan config:clear

# Clear all
railway run php artisan optimize:clear
```

### Run Migrations

```bash
# After database schema changes
railway run php artisan migrate
```

### SSH Access

```bash
# Shell into container
railway shell

# Run commands directly
php artisan tinker
php artisan migrate
composer show
```

---

## Scaling & Performance

### Current Setup (Free Tier)

- Single-process PHP server
- File-based cache
- Database-based queue
- No Redis/Memcached
- Good for: ~100-200 concurrent users

### Upgrade to Paid Tier

Add Redis service for better performance:

```env
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=redis.railway.internal
```

Benefits:
- Session caching
- Queue processing
- Better performance
- Horizontal scaling ready

---

## Troubleshooting Resources

- Railway Docs: https://docs.railway.app
- Laravel Docs: https://laravel.com/docs
- Vite Docs: https://vitejs.dev/guide/
- GitHub Issues: Check commits for error messages

---

Generated: 2026-05-28 | Status: ✅ Production-Ready
