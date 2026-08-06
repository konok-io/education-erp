@echo off
title Education ERP - Starting...

echo.
echo ============================================
echo    Education ERP - Starting
echo ============================================
echo.

REM Start PHP Server
echo [1/2] Starting PHP Server...
cd /d C:\laragon\www\education-erp
start "Education ERP" cmd /k "cd backend && php artisan serve --host=127.0.0.1 --port=8000"

REM Wait a moment
timeout /t 2 /nobreak >nul

REM Open Browser
echo [2/2] Opening Browser...
start http://education-erp.test

echo.
echo ============================================
echo    Education ERP is starting!
echo ============================================
echo.
echo URL: http://education-erp.test
echo.
echo Close this window when done.
echo ============================================
echo.

pause
