# SKYBOOK RAILWAY DEPLOYMENT - MASTER CHECKLIST ✅

## Pre-Deployment Tasks

### 1. Local Verification
- [ ] Run `npm run build` → verify `public/build/manifest.json` exists
- [ ] Run `php artisan config:cache` → no errors
- [ ] Run `php artisan route:cache` → no errors
- [ ] Run `php artisan storage:link` → no errors
- [ ] Test locally: `php -S 127.0.0.1:8000 -t public`
- [ ] Visit http://127.0.0.1:8000/up → expect "pong" response
- [ ] Check all assets load (CSS, JS, images)
- [ ] Test key API endpoints (if applicable)

### 2. Generate APP_KEY
```bash
php artisan key:generate --show
# SAVE THIS VALUE - you'll need it for Railway
```
- [ ] Note down the `base64:...` value

### 3. Git Commit
```bash
git add .
git commit -m "Fix: Production-ready Railway deployment

- Remove php artisan serve from production
- Remove npm run dev from production
- Add Vite manifest generation
- Add PHP 8.3 complete extension set
- Add Laravel caching (config, routes, views)
- Add storage link handling
- Configure file-based cache/session for free tier
- Fix healthcheck endpoint
- Add production environment template"
```
- [ ] Committed all changes
- [ ] No uncommitted changes: `git status` shows clean

### 4. Set Railway Environment Variables

**In Railway Dashboard → Project Settings → Variables:**

#### App Configuration
```
APP_NAME=SkyBook
APP_ENV=production
APP_KEY=base64:YOUR_KEY_FROM_STEP_2
APP_DEBUG=false
APP_URL=https://skybook.railway.app
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```
- [ ] APP_NAME set
- [ ] APP_ENV=production
- [ ] APP_KEY set (your generated key)
- [ ] APP_DEBUG=false
- [ ] APP_URL set to your Railway domain

#### Logging
```
LOG_LEVEL=info
LOG_CHANNEL=stack
```
- [ ] LOG_LEVEL=info
- [ ] LOG_CHANNEL=stack

#### Cache & Session (Free Tier)
```
CACHE_STORE=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
```
- [ ] CACHE_STORE=file
- [ ] SESSION_DRIVER=file

#### Queue
```
QUEUE_CONNECTION=database
```
- [ ] QUEUE_CONNECTION=database

#### Database (Link to Railway MySQL)
```
DB_CONNECTION=mysql
DB_HOST=[auto-populated by Railway]
DB_PORT=[auto-populated by Railway]
DB_DATABASE=[auto-populated by Railway]
DB_USERNAME=[auto-populated by Railway]
DB_PASSWORD=[auto-populated by Railway]
```
- [ ] MySQL service linked to project
- [ ] DB_* variables auto-populated or manually set
- [ ] Test connection: `railway run php artisan tinker` → `DB::connection()->getPdo();`

#### Email (Optional)
```
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@skybook.app
MAIL_FROM_NAME=SkyBook
```
- [ ] MAIL_* configured (optional)

#### Vite
```
VITE_APP_NAME=SkyBook
```
- [ ] VITE_APP_NAME set

### 5. Database Preparation

#### Option A: Fresh Migration
```bash
railway run php artisan migrate --force
```
- [ ] Migrations completed successfully
- [ ] No migration errors in logs

#### Option B: Restore from Backup
```bash
# If you have a backup
railway run mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE < backup.sql
```
- [ ] Data restored
- [ ] Verify tables exist: `railway run php artisan tinker` → `DB::table('users')->count();`

### 6. Verify Configuration Files

Files to check are updated:
- [ ] `nixpacks.toml` - contains PHP extensions, proper build phases
- [ ] `vite.config.js` - has manifest generation enabled
- [ ] `composer.json` - removed `php artisan serve` from dev script
- [ ] `Procfile` - exists with `web:` process
- [ ] `.env.production` - exists as template

---

## Deployment

### Step 1: Push to Railway
```bash
git push railway main
```
- [ ] Push successful
- [ ] Railway build started (check Dashboard)

### Step 2: Monitor Build
```bash
railway logs --follow
```

Expected output (in order):
```
1. [Build] Installing dependencies
   ✓ Composer install
   ✓ npm install

2. [Build] Building assets
   ✓ npm run build
   ✓ public/build/manifest.json created

3. [Build] Caching
   ✓ php artisan config:cache
   ✓ php artisan route:cache
   ✓ php artisan view:cache

4. [Build] Storage
   ✓ php artisan storage:link

5. [Build] Optimization
   ✓ php artisan optimize

6. [Start] Server starting
   ✓ Listening on 0.0.0.0:PORT
   ✓ Ready to accept requests

7. [Health] Healthcheck passing
   ✓ GET /up → 200 OK
```

- [ ] Build phase completed without errors
- [ ] Start phase completed
- [ ] Healthcheck passing
- [ ] No critical errors in logs

---

## Post-Deployment Verification

### 1. Health Endpoint (Critical)
```bash
curl https://skybook.railway.app/up -v
```
Expected: `200 OK` with response body
- [ ] Returns 200 OK
- [ ] Responds within 5 seconds
- [ ] No error messages

### 2. Homepage
```bash
curl https://skybook.railway.app -v
```
Expected: `200 OK` with HTML content
- [ ] Returns 200 OK
- [ ] Contains HTML markup
- [ ] Loads without errors

### 3. Vite Assets
```bash
curl https://skybook.railway.app/build/manifest.json -v
```
Expected: `200 OK` with JSON manifest
- [ ] Returns 200 OK
- [ ] Is valid JSON
- [ ] Contains asset mappings

### 4. CSS/JS Assets
Visit https://skybook.railway.app in browser:
- [ ] Open DevTools (F12)
- [ ] Check Network tab
- [ ] CSS files load from `/build/` (200 OK)
- [ ] JS files load from `/build/` (200 OK)
- [ ] No 404 errors for assets
- [ ] Page renders correctly

### 5. API Endpoints (if applicable)
```bash
# Example: Test API endpoint
curl https://skybook.railway.app/api/users -H "Authorization: Bearer TOKEN"
```
- [ ] API responds correctly
- [ ] Authentication works
- [ ] Database queries work

### 6. Database Connection
```bash
railway run php artisan tinker
# In Tinker:
>>> DB::connection()->getPdo();
>>> DB::table('users')->count();
>>> exit();
```
- [ ] PDO connection successful
- [ ] Can query tables
- [ ] Data accessible

### 7. Storage/File Uploads
If your app uploads files:
```bash
# Test upload endpoint
curl -X POST https://skybook.railway.app/api/upload \
  -F "file=@test.jpg"
```
- [ ] File upload succeeds
- [ ] File stored in public/storage/
- [ ] File accessible via URL

### 8. Logs Check
```bash
railway logs --limit 200 | grep -i error
```
- [ ] No critical errors
- [ ] No "crash" messages
- [ ] No "failed to" messages
- [ ] Application running normally

---

## Performance Testing

### 1. Response Time (target: <200ms)
```bash
time curl https://skybook.railway.app/up
```
- [ ] Response under 200ms

### 2. Asset Load Time (target: <100ms)
In browser DevTools:
- [ ] CSS files: <100ms
- [ ] JS files: <100ms
- [ ] Images: <100ms (first load)

### 3. Concurrent Requests
```bash
# Using Apache Bench
ab -n 100 -c 10 https://skybook.railway.app/

# Should show no failed requests
```
- [ ] 0 failed requests
- [ ] Avg response time reasonable

---

## Rollback Plan (If Issues)

### If Build Fails
```bash
# Check logs for specific error
railway logs --limit 100

# Common fixes:
railway run php artisan migrate --fresh --force
railway run php artisan cache:clear
railway run php artisan config:clear

# Redeploy
git push railway main
```
- [ ] Issue identified from logs
- [ ] Cache cleared
- [ ] Redeployed successfully

### If App Crashes After Deploy
```bash
# Option 1: Clear caches and redeploy
railway run php artisan cache:clear
railway run php artisan config:clear
railway redeploy

# Option 2: Revert to previous commit
git revert HEAD
git push railway main

# Option 3: Manual intervention
railway shell
php artisan migrate:rollback
php artisan cache:clear
exit
railway redeploy
```
- [ ] Issue resolved
- [ ] App back online

---

## Ongoing Maintenance

### Daily Monitoring
```bash
# Check logs for errors
railway logs --tail 50 | grep -i error

# Monitor resource usage
railway metrics
```
- [ ] No error spikes
- [ ] Memory/CPU usage reasonable

### Weekly Tasks
- [ ] Check application logs for warnings
- [ ] Test critical user journeys
- [ ] Monitor database size
- [ ] Review error tracking (if configured)

### Monthly Tasks
- [ ] Clear old logs: `railway run php artisan logs:clear`
- [ ] Analyze performance trends
- [ ] Plan scaling if needed
- [ ] Update dependencies: `composer update`, `npm update`

---

## Emergency Contacts & Resources

### Documentation
- Railway Docs: https://docs.railway.app
- Laravel Docs: https://laravel.com/docs/11
- Vite Docs: https://vitejs.dev/guide/

### Debug Commands
```bash
# View real-time logs
railway logs --follow

# SSH into container
railway shell

# Run artisan commands
railway run php artisan migrate
railway run php artisan cache:clear
railway run php artisan config:clear

# Check environment variables
railway variables

# Restart application
railway redeploy
```

### Common Issues Reference
See: `DEPLOYMENT_GUIDE.md` → "Troubleshooting Common Issues"

---

## Sign-Off

- [ ] All pre-deployment checks passed
- [ ] Deployment successful
- [ ] All post-deployment verification completed
- [ ] Performance acceptable
- [ ] Rollback plan documented
- [ ] Team notified of deployment

**Deployment Date:** ________________
**Deployed By:** ________________
**Status:** ✅ PRODUCTION LIVE
**Ready for Users:** ________________

---

**Version:** 1.0
**Last Updated:** 2026-05-28
**Status:** COMPLETE ✅
