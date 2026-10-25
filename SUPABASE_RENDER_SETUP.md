# Deployment Guide — Supabase (PostgreSQL) + Render (Laravel)

Both platforms are **free** and do not require a credit card.

---

## Part 1 — Supabase (Free PostgreSQL Database)

### 1. Create a Supabase account
- Go to https://supabase.com and sign up with GitHub

### 2. Create a new project
- Click **New project**
- Name: `alamada-cms`
- Database Password: choose a strong password and **save it**
- Region: **Southeast Asia (Singapore)**
- Click **Create new project** (takes ~2 minutes)

### 3. Get your connection details
- Go to your project → **Settings** → **Database**
- Scroll to **Connection string** → select **URI** tab
- Copy the URI — it looks like:
  ```
  postgresql://postgres.[PROJECT-REF]:[PASSWORD]@aws-0-ap-southeast-1.pooler.supabase.com:5432/postgres
  ```
- Also note the individual values under **Connection parameters**:
  - **Host**: `aws-0-ap-southeast-1.pooler.supabase.com`
  - **Database**: `postgres`
  - **User**: `postgres.[PROJECT-REF]`
  - **Password**: the one you set above
  - **Port**: `5432`

---

## Part 2 — Render (Free Laravel Hosting)

### 1. Create a Render account
- Go to https://render.com and sign up with GitHub

### 2. Create a new Web Service
- Click **New** → **Web Service**
- Connect your GitHub repository: `Emperado7/alamada-child-monitoring-system`
- Configure:
  - **Name**: `alamada-cms-backend`
  - **Root Directory**: `backend`
  - **Environment**: `Docker`
  - **Plan**: `Free`
- Click **Create Web Service**

### 3. Set environment variables in Render
Go to your service → **Environment** → add these one by one:

| Key | Value |
|-----|-------|
| `APP_KEY` | Run `php artisan key:generate --show` locally |
| `APP_URL` | `https://alamada-cms-backend.onrender.com` (Render shows this) |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | from Supabase (e.g. `aws-0-ap-southeast-1.pooler.supabase.com`) |
| `DB_PORT` | `5432` |
| `DB_DATABASE` | `postgres` |
| `DB_USERNAME` | from Supabase (e.g. `postgres.abcdefghijklmnop`) |
| `DB_PASSWORD` | your Supabase database password |
| `DB_SSLMODE` | `require` |
| `SANCTUM_STATEFUL_DOMAINS` | `alamada-cms-backend.onrender.com` |
| `SESSION_DOMAIN` | `.onrender.com` |

### 4. Get the Deploy Hook URL
- Go to your Render service → **Settings** → scroll to **Deploy Hook**
- Copy the URL (looks like `https://api.render.com/deploy/srv-xxx?key=yyy`)

### 5. Add to GitHub Secrets
Go to your GitHub repo → **Settings** → **Secrets and variables** → **Actions** → **New repository secret**:

| Secret | Value |
|--------|-------|
| `RENDER_DEPLOY_HOOK_URL` | the URL from step 4 above |

---

## Part 3 — Automatic Deploy

After completing Parts 1 and 2, every push to `master` or `main`:
1. GitHub Actions calls the Render deploy hook
2. Render pulls the latest code and rebuilds the Docker container
3. On startup, `start.sh` automatically runs:
   - `php artisan migrate --force`
   - `php artisan db:seed --force`
   - Starts the Laravel server

### Verify it works
- Health check: `https://alamada-cms-backend.onrender.com/api/health`
- Should return: `{"status":"ok","db":"connected"}`

---

## Part 4 — Update Android App

Once you have the Render URL, update the base URL in the Android app:
- File: `android/app/src/main/java/com/alamada/childmonitor/network/ApiClient.java`
- Change: `BASE_URL = "https://alamada-cms-backend.onrender.com/api/"`

---

## Free Tier Limits

| Platform | Limit |
|----------|-------|
| Supabase | 500 MB database, 2 GB bandwidth/month |
| Render   | 750 hours/month (enough for 1 service), spins down after 15 min inactivity |

> **Note on Render free tier**: The service sleeps after 15 minutes of inactivity.
> The first request after sleep takes ~30 seconds to wake up.
> This is normal on the free plan.
