@echo off
title Education ERP - Starting Servers...

echo.
echo ============================================
echo    Education ERP - Starting Servers
echo ============================================
echo.

REM Start Backend Server
echo [1/2] Starting Backend Server...
cd /d C:\laragon\www\education-erp\backend
start "Education ERP - Backend" cmd /k "php artisan serve --host=127.0.0.1 --port=8000"

REM Wait a moment
timeout /t 2 /nobreak >nul

REM Start Frontend Server
echo [2/2] Starting Frontend Server...
cd /d C:\laragon\www\education-erp\frontend
start "Education ERP - Frontend" cmd /k "npm run dev"

REM Wait for servers to start
echo.
echo Waiting for servers to start...
timeout /t 5 /nobreak >nul

REM Open Browser
echo.
echo Opening Browser...
start http://education-erp.test

echo.
echo ============================================
echo    Servers are starting!
echo ============================================
echo.
echo Backend:  http://127.0.0.1:8000
echo Frontend: http://education-erp.test
echo.
echo Close this window when done.
echo ============================================
echo.

pause
