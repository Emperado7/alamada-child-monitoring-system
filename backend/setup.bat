@echo off
setlocal enabledelayedexpansion
title Alamada LGU Child Monitoring System — Setup

echo.
echo  ============================================================
echo   Alamada LGU Child Monitoring System — Backend Setup
echo  ============================================================
echo.

:: ── Check PHP ─────────────────────────────────────────────────
where php >nul 2>&1
if errorlevel 1 (
    echo  [ERROR] PHP not found. Make sure XAMPP is installed and
    echo          C:\xampp\php is added to your PATH.
    pause & exit /b 1
)

:: ── Check Composer ────────────────────────────────────────────
where composer >nul 2>&1
if errorlevel 1 (
    echo  [ERROR] Composer not found. Download from https://getcomposer.org
    pause & exit /b 1
)

echo  [1/7] Checking XAMPP MySQL is running...
ping -n 1 127.0.0.1 >nul

echo  [2/7] Copying environment file...
if not exist .env (
    copy .env.example .env >nul
    echo         .env created from .env.example
) else (
    echo         .env already exists — skipping.
)

echo.
echo  IMPORTANT: Open .env and set your database password if needed.
echo  Default: DB_DATABASE=alamada_cms  DB_USERNAME=root  DB_PASSWORD=
echo.
pause

echo  [3/7] Installing Composer dependencies...
composer install --no-interaction --prefer-dist --optimize-autoloader
if errorlevel 1 (
    echo  [ERROR] composer install failed.
    pause & exit /b 1
)

echo  [4/7] Generating application key...
php artisan key:generate --ansi

echo  [5/7] Creating storage symlink...
php artisan storage:link

echo  [6/7] Running database migrations...
php artisan migrate --force
if errorlevel 1 (
    echo  [ERROR] Migration failed. Make sure MySQL is running and
    echo          the database "alamada_cms" exists in phpMyAdmin.
    pause & exit /b 1
)

echo  [7/7] Seeding demo data...
php artisan db:seed --force

echo.
echo  ============================================================
echo   Setup complete!
echo.
echo   Start the server:   php artisan serve
echo   Web URL:            http://localhost:8000
echo   API base URL:       http://localhost:8000/api
echo.
echo   Demo login accounts:
echo     Admin   admin@alamada-lgu.gov.ph   / Admin@1234
echo     Staff   maria.santos@alamada-lgu.gov.ph / Staff@1234
echo     Parent  ana.delacruz@gmail.com     / Parent@1234
echo  ============================================================
echo.
pause
