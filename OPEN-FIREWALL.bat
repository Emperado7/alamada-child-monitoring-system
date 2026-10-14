@echo off
:: This script must run as Administrator
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo Requesting Administrator privileges...
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)

echo.
echo  Adding firewall rule for port 8000...
netsh advfirewall firewall add rule name="Laravel 8000" dir=in action=allow protocol=TCP localport=8000

echo.
echo  Done! Port 8000 is now open.
echo  Other devices can connect at: http://192.168.0.57:8000
echo.
pause
