@echo off
REM Education ERP - Laragon Setup Script (Windows)
REM Run this as Administrator

echo ==========================================
echo    Education ERP - Laragon Setup
echo ==========================================

set LARAGON_WWW=C:\laragon\www\education-erp
set LARAGON_NGINX=C:\laragon\etc\nginx\sites-enabled

REM Check if project exists
if not exist "%LARAGON_WWW%" (
    echo [ERROR] Project not found at %LARAGON_WWW%
    echo Please copy your project to C:\laragon\www\education-erp
    pause
    exit /b 1
)

echo [OK] Project found at %LARAGON_WWW%

REM 1. Copy Nginx config
echo.
echo [1/4] Copying Nginx configuration...
copy /Y "auto.education-erp.test.conf" "%LARAGON_NGINX%\" >nul
if %errorlevel% equ 0 (
    echo [OK] Nginx config copied
) else (
    echo [ERROR] Failed to copy Nginx config
)

REM 2. Add hosts entries
echo.
echo [2/4] Adding hosts entries...
findstr /C:"education-erp.test" C:\Windows\System32\drivers\etc\hosts >nul
if %errorlevel% equ 0 (
    echo [OK] Hosts entries already exist
) else (
    echo 127.0.0.1    education-erp.test >> C:\Windows\System32\drivers\etc\hosts
    echo 127.0.0.1    api.education-erp.test >> C:\Windows\System32\drivers\etc\hosts
    echo [OK] Hosts entries added
)

REM 3. Install npm dependencies and build frontend
echo.
echo [3/4] Building frontend...
cd /d "%LARAGON_WWW%\frontend"
if exist "node_modules" (
    call npm run build
    echo [OK] Frontend built
) else (
    echo [WARNING] node_modules not found
    echo Please run: cd frontend ^&^& npm install
)

REM 4. Setup Laravel
echo.
echo [4/4] Laravel setup...
cd /d "%LARAGON_WWW%\backend"
if exist ".env" (
    echo [OK] .env file exists
) else (
    echo [WARNING] .env file not found
    echo Please create it:
    echo   copy .env.example .env
    echo   php artisan key:generate
)

echo.
echo ==========================================
echo    Setup Complete!
echo ==========================================
echo.
echo Next steps:
echo 1. Restart Laragon (Stop All -^> Start All)
echo 2. Open two terminals:
echo.
echo    Terminal 1 - Backend API:
echo    cd C:\laragon\www\education-erp\backend
echo    php artisan serve --host=api.education-erp.test --port=8000
echo.
echo    Terminal 2 - Frontend Dev:
echo    cd C:\laragon\www\education-erp\frontend
echo    npm run dev
echo.
echo 3. Open browser:
echo    Frontend: http://education-erp.test
echo    API:      http://api.education-erp.test
echo.
pause
