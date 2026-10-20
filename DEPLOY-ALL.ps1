# ═══════════════════════════════════════════════════════════
#  Alamada CMS — Full Auto Deploy to Railway
#  Run this once to deploy. After that, just git push.
# ═══════════════════════════════════════════════════════════

Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned -Force

$ErrorActionPreference = "Stop"
$ProjectDir = "C:\A MOBILE BASED MONITORING SYSTEM OF CHILDREN AT ALAMADA LGU-LEARNING CENTER"

Write-Host ""
Write-Host "============================================================" -ForegroundColor Green
Write-Host "  ALAMADA CMS - FULL AUTO DEPLOY TO RAILWAY" -ForegroundColor Green
Write-Host "  Your system will be live on the internet in ~5 minutes" -ForegroundColor Green
Write-Host "============================================================" -ForegroundColor Green
Write-Host ""

Set-Location $ProjectDir

# ── Step 1: Push latest code to GitHub ───────────────────────
Write-Host "[1/6] Pushing latest code to GitHub..." -ForegroundColor Cyan
git add -A
git commit -m "Auto-deploy: $(Get-Date -Format 'yyyy-MM-dd HH:mm')" 2>$null
git push origin master
Write-Host "  GitHub up to date." -ForegroundColor Green
Write-Host ""

# ── Step 2: Railway Login ─────────────────────────────────────
Write-Host "[2/6] Logging in to Railway..." -ForegroundColor Cyan
Write-Host "  A browser will open. Sign in with GitHub." -ForegroundColor Yellow
Write-Host ""
railway login
Write-Host "  Logged in." -ForegroundColor Green
Write-Host ""

# ── Step 3: Link or create Railway project ───────────────────
Write-Host "[3/6] Setting up Railway project..." -ForegroundColor Cyan

$linked = railway status 2>$null
if ($LASTEXITCODE -ne 0) {
    Write-Host "  Creating new Railway project..." -ForegroundColor Yellow
    railway init --name "alamada-cms"
} else {
    Write-Host "  Already linked to Railway project." -ForegroundColor Green
}
Write-Host ""

# ── Step 4: Set all environment variables ────────────────────
Write-Host "[4/6] Setting environment variables..." -ForegroundColor Cyan

$vars = @{
    "APP_NAME"          = "Alamada LGU Child Monitoring System"
    "APP_ENV"           = "production"
    "APP_KEY"           = "base64:dN5eYOE6wE//4PaqAFuVDlRcm8t2fL/BmZIz3n6GaMk="
    "APP_DEBUG"         = "false"
    "LOG_CHANNEL"       = "stack"
    "LOG_LEVEL"         = "error"
    "DB_CONNECTION"     = "mysql"
    "CACHE_DRIVER"      = "file"
    "FILESYSTEM_DISK"   = "public"
    "QUEUE_CONNECTION"  = "sync"
    "SESSION_DRIVER"    = "file"
    "SESSION_LIFETIME"  = "120"
    "BCRYPT_ROUNDS"     = "10"
}

foreach ($key in $vars.Keys) {
    railway variables set "$key=$($vars[$key])" 2>$null
    Write-Host "  Set: $key" -ForegroundColor DarkGray
}

Write-Host "  All variables set." -ForegroundColor Green
Write-Host ""

# ── Step 5: Deploy ────────────────────────────────────────────
Write-Host "[5/6] Deploying to Railway..." -ForegroundColor Cyan
Write-Host "  This takes 3-5 minutes. Please wait..." -ForegroundColor Yellow
Write-Host ""
railway up --detach
Write-Host ""
Write-Host "  Deployment started!" -ForegroundColor Green
Write-Host ""

# ── Step 6: Get public URL ────────────────────────────────────
Write-Host "[6/6] Getting your public URL..." -ForegroundColor Cyan
Write-Host ""

Start-Sleep -Seconds 5

$domain = railway domain 2>$null
if ($domain) {
    Write-Host "  Your live URL: $domain" -ForegroundColor Green
} else {
    Write-Host "  Generating domain..." -ForegroundColor Yellow
    railway domain --generate 2>$null
    Start-Sleep -Seconds 3
    railway domain
}

Write-Host ""
Write-Host "============================================================" -ForegroundColor Green
Write-Host "  DEPLOYMENT COMPLETE!" -ForegroundColor Green
Write-Host ""
Write-Host "  Your system is now live on the INTERNET." -ForegroundColor White
Write-Host "  Anyone can use it from iPhone, Android, PC" -ForegroundColor White
Write-Host "  WITHOUT needing the same WiFi!" -ForegroundColor White
Write-Host ""
Write-Host "  To update the system in future:" -ForegroundColor Yellow
Write-Host "  Just run: git push origin master" -ForegroundColor Yellow
Write-Host "  Railway auto-deploys every time you push." -ForegroundColor Yellow
Write-Host "============================================================" -ForegroundColor Green
Write-Host ""

# Open Railway dashboard
railway open
