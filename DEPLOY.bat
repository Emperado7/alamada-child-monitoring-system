@echo off
title Alamada CMS - Auto Deploy to Railway
color 0A

echo.
echo  ============================================================
echo   ALAMADA CMS - AUTOMATIC DEPLOYMENT TO RAILWAY
echo   This will make your system accessible from ANYWHERE
echo  ============================================================
echo.

:: ── Check Node.js ─────────────────────────────────────────────
where node >nul 2>&1
if errorlevel 1 (
    echo  [ERROR] Node.js not found.
    echo  Please install Node.js from: https://nodejs.org
    echo  Then run this script again.
    pause
    exit /b 1
)

:: ── Check Git ─────────────────────────────────────────────────
where git >nul 2>&1
if errorlevel 1 (
    echo  [ERROR] Git not found.
    echo  Please install Git from: https://git-scm.com
    pause
    exit /b 1
)

echo  [OK] Node.js and Git found.
echo.

:: ── Install Railway CLI ───────────────────────────────────────
echo  [1/5] Installing Railway CLI...
call npm install -g @railway/cli >nul 2>&1
if errorlevel 1 (
    echo  Trying alternative install...
    call npm install -g @railway/cli
)
echo  Railway CLI ready.
echo.

:: ── Railway Login ─────────────────────────────────────────────
echo  [2/5] Logging in to Railway...
echo  A browser window will open - sign in with GitHub or email.
echo.
call railway login
if errorlevel 1 (
    echo  [ERROR] Railway login failed.
    pause
    exit /b 1
)
echo  Logged in successfully.
echo.

:: ── Initialize Railway Project ────────────────────────────────
echo  [3/5] Creating Railway project...
cd /d "C:\A MOBILE BASED MONITORING SYSTEM OF CHILDREN AT ALAMADA LGU-LEARNING CENTER"
call railway init --name "alamada-cms"
echo  Project created.
echo.

:: ── Add MySQL Database ────────────────────────────────────────
echo  [4/5] Adding MySQL database...
call railway add --plugin mysql
echo  MySQL database added.
echo.

:: ── Set Environment Variables ─────────────────────────────────
echo  [5/5] Setting environment variables...

call railway variables set APP_NAME="Alamada LGU Child Monitoring System"
call railway variables set APP_ENV=production
call railway variables set APP_KEY=base64:dN5eYOE6wE//4PaqAFuVDlRcm8t2fL/BmZIz3n6GaMk=
call railway variables set APP_DEBUG=false
call railway variables set LOG_CHANNEL=stack
call railway variables set LOG_LEVEL=error
call railway variables set DB_CONNECTION=mysql
call railway variables set CACHE_DRIVER=file
call railway variables set FILESYSTEM_DISK=public
call railway variables set QUEUE_CONNECTION=sync
call railway variables set SESSION_DRIVER=file
call railway variables set SESSION_LIFETIME=120

echo  Environment variables set.
echo.

:: ── Deploy ────────────────────────────────────────────────────
echo  Deploying to Railway... (this takes 2-5 minutes)
echo.
call railway up --detach

echo.
echo  Getting your public URL...
call railway domain
echo.

echo  ============================================================
echo   DEPLOYMENT COMPLETE!
echo.
echo   Your system is now live on the internet.
echo   Anyone can access it from ANYWHERE without WiFi.
echo.
echo   Run: railway open   (to open in browser)
echo   Run: railway logs   (to check server logs)
echo  ============================================================
echo.
pause
