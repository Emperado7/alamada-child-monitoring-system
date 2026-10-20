@echo off
title Alamada CMS - Server
color 0A

set PHP=C:\xampp\php\php\php.exe
set BACKEND=C:\A MOBILE BASED MONITORING SYSTEM OF CHILDREN AT ALAMADA LGU-LEARNING CENTER\backend
set LOCAL_IP=192.168.100.26

:: ── Request Admin for firewall ────────────────────────────────
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo Requesting Administrator privileges...
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)

:: ── Kill any existing php server on port 8000 ─────────────────
echo  [1/3] Stopping any existing server on port 8000...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr ":8000 "') do (
    taskkill /F /PID %%a >nul 2>&1
)
timeout /t 1 /nobreak >nul
echo  Done.

:: ── Open firewall ─────────────────────────────────────────────
echo.
echo  [2/3] Opening firewall port 8000 for all devices...
netsh advfirewall firewall delete rule name="Laravel 8000"     >nul 2>&1
netsh advfirewall firewall delete rule name="Laravel 8000 OUT" >nul 2>&1
netsh advfirewall firewall add rule name="Laravel 8000"     dir=in  action=allow protocol=TCP localport=8000 >nul 2>&1
netsh advfirewall firewall add rule name="Laravel 8000 OUT" dir=out action=allow protocol=TCP localport=8000 >nul 2>&1
echo  Firewall ready.

:: ── Start server on 0.0.0.0 (all interfaces) ─────────────────
echo.
echo  [3/3] Starting server...
echo.
echo  ============================================================
echo   SYSTEM IS RUNNING - ACCESSIBLE ON ALL DEVICES
echo.
echo   This PC:          http://localhost:8000
echo   iPhone/Android:   http://%LOCAL_IP%:8000
echo   Other PCs:        http://%LOCAL_IP%:8000
echo.
echo   Admin:   admin@alamada-lgu.gov.ph  /  Admin@1234
echo   Staff:   maamjoy@alamada-lgu.gov.ph  /  Staff@1234
echo.
echo   All devices MUST be on the same WiFi as this PC.
echo   Press Ctrl+C to stop.
echo  ============================================================
echo.

:: Open browser on this PC
start "" "http://localhost:8000"

:: Start Laravel bound to ALL network interfaces
cd /d "%BACKEND%"
"%PHP%" artisan serve --host=0.0.0.0 --port=8000

pause
