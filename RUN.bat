@echo off
title Alamada LGU Child Monitoring System
color 0A

set PHP=C:\xampp\php\php\php.exe
set MYSQL=C:\xampp\php\mysql\bin\mysql.exe
set MYSQLD=C:\xampp\php\mysql\bin\mysqld.exe
set BACKEND=C:\A MOBILE BASED MONITORING SYSTEM OF CHILDREN AT ALAMADA LGU-LEARNING CENTER\backend
set LOCAL_IP=192.168.100.26

echo.
echo  ============================================================
echo   ALAMADA LGU CHILD MONITORING SYSTEM
echo  ============================================================
echo.

:: ── Auto-request Admin for firewall ──────────────────────
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo  Requesting Administrator privileges for firewall setup...
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)

:: ── Step 1: Open Firewall for port 8000 ──────────────────
echo  [1/5] Opening firewall port 8000...
netsh advfirewall firewall delete rule name="Laravel 8000" >nul 2>&1
netsh advfirewall firewall delete rule name="Laravel 8000 OUT" >nul 2>&1
netsh advfirewall firewall add rule name="Laravel 8000"     dir=in  action=allow protocol=TCP localport=8000 >nul 2>&1
netsh advfirewall firewall add rule name="Laravel 8000 OUT" dir=out action=allow protocol=TCP localport=8000 >nul 2>&1
echo  Firewall ready.

:: ── Kill any existing php on port 8000 ───────────────────
echo.
echo  Stopping any old server on port 8000...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr ":8000 "') do (
    taskkill /F /PID %%a >nul 2>&1
)
timeout /t 1 /nobreak >nul

:: ── Step 2: Start MySQL if not running ───────────────────
echo.
echo  [2/5] Starting MySQL...
"%MYSQLD%" --standalone --datadir="C:\xampp\php\mysql\data" >nul 2>&1 &
timeout /t 3 /nobreak >nul

"%MYSQL%" -u root --connect-timeout=5 -e "SELECT 1;" >nul 2>&1
if errorlevel 1 (
    echo  WARNING: MySQL may still be starting...
    timeout /t 3 /nobreak >nul
) else (
    echo  MySQL is running.
)

:: ── Step 3: Create database ───────────────────────────────
echo.
echo  [3/5] Creating database...
"%MYSQL%" -u root -e "CREATE DATABASE IF NOT EXISTS alamada_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>&1
echo  Database ready.

:: ── Step 4: Run migrations and clear config ───────────────
echo.
echo  [4/5] Running migrations...
cd /d "%BACKEND%"
"%PHP%" artisan migrate --force >nul 2>&1
"%PHP%" artisan storage:link >nul 2>&1
"%PHP%" artisan config:clear >nul 2>&1
"%PHP%" artisan cache:clear >nul 2>&1
"%PHP%" artisan route:clear >nul 2>&1
echo  Migrations complete.

:: ── Step 5: Start server ──────────────────────────────────
echo.
echo  [5/5] Starting web server...
echo.
echo  ============================================================
echo   SYSTEM IS RUNNING - ACCESSIBLE ON ALL DEVICES
echo.
echo   This PC (Desktop):   http://localhost:8000
echo   iPhone / Android:    http://%LOCAL_IP%:8000
echo   Other devices:       http://%LOCAL_IP%:8000
echo   Android API:         http://%LOCAL_IP%:8000/api
echo.
echo   Admin:  admin@alamada-lgu.gov.ph  /  Admin@1234
echo   Staff:  maamjoy@alamada-lgu.gov.ph  /  Staff@1234
echo   Staff:  jose.reyes@alamada-lgu.gov.ph  /  Staff@1234
echo.
echo   Make sure your phone is on the SAME WiFi as this PC.
echo   Press Ctrl+C to stop the server.
echo  ============================================================
echo.

:: Open browser on this PC
start "" "http://localhost:8000"

:: Start Laravel server bound to all interfaces (0.0.0.0 = all devices)
"%PHP%" artisan serve --host=0.0.0.0 --port=8000

pause
