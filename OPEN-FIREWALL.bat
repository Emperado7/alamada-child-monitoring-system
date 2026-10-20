@echo off
:: This script must run as Administrator
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo Requesting Administrator privileges...
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)

echo.
echo  Removing old firewall rule (if any)...
netsh advfirewall firewall delete rule name="Laravel 8000" >nul 2>&1

echo  Adding firewall rule for port 8000...
netsh advfirewall firewall add rule name="Laravel 8000" dir=in action=allow protocol=TCP localport=8000

echo  Adding firewall rule for port 8000 (outbound)...
netsh advfirewall firewall delete rule name="Laravel 8000 OUT" >nul 2>&1
netsh advfirewall firewall add rule name="Laravel 8000 OUT" dir=out action=allow protocol=TCP localport=8000

echo.
echo  Done! Port 8000 is now open for all devices.
echo  Connect using: http://192.168.100.26:8000
echo.
pause
