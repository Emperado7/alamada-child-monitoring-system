@echo off
title Alamada LGU Child Monitoring System
color 0A

set PHP=C:\xampp\php\php\php.exe
set MYSQL=C:\xampp\php\mysql\bin\mysql.exe
set MYSQLD=C:\xampp\php\mysql\bin\mysqld.exe
set BACKEND=C:\A MOBILE BASED MONITORING SYSTEM OF CHILDREN AT ALAMADA LGU-LEARNING CENTER\backend

echo.
echo  ============================================================
echo   ALAMADA LGU CHILD MONITORING SYSTEM
echo  ============================================================
echo.

:: ── Step 1: Start MySQL if not running ───────────────────
echo  [1/4] Starting MySQL...
"%MYSQLD%" --standalone --datadir="C:\xampp\php\mysql\data" >nul 2>&1 &
timeout /t 3 /nobreak >nul

:: Verify MySQL is up
"%MYSQL%" -u root --connect-timeout=5 -e "SELECT 1;" >nul 2>&1
if errorlevel 1 (
    echo  WARNING: MySQL may still be starting...
    timeout /t 3 /nobreak >nul
) else (
    echo  MySQL is running.
)

:: ── Step 2: Create database ───────────────────────────────
echo.
echo  [2/4] Creating database...
"%MYSQL%" -u root -e "CREATE DATABASE IF NOT EXISTS alamada_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>&1
echo  Database ready.

:: ── Step 3: Run migrations ────────────────────────────────
echo.
echo  [3/4] Running migrations and seeding...
cd /d "%BACKEND%"
"%PHP%" artisan migrate --force >nul 2>&1
"%PHP%" artisan db:seed --force >nul 2>&1
"%PHP%" artisan storage:link >nul 2>&1
"%PHP%" artisan config:clear >nul 2>&1
"%PHP%" artisan cache:clear >nul 2>&1
echo  Migrations complete.

:: ── Step 4: Start server ──────────────────────────────────
echo.
echo  [4/4] Starting web server...
echo.
echo  ============================================================
echo   SYSTEM IS RUNNING
echo.
echo   Open browser:  http://127.0.0.1:8000
echo.
echo   Admin Login:   admin@alamada-lgu.gov.ph   / Admin@1234
echo   Staff Login:   maamjoy@alamada-lgu.gov.ph  / Staff@1234
echo.
echo   Press Ctrl+C to stop.
echo  ============================================================
echo.

:: Open browser
start "" "http://127.0.0.1:8000"

:: Start Laravel server
"%PHP%" artisan serve --host=127.0.0.1 --port=8000

pause
