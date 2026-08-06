@echo off
title Education ERP - Stopping Servers...

echo.
echo ============================================
echo    Education ERP - Stopping Servers
echo ============================================
echo.

REM Kill PHP artisan servers
echo Stopping Backend Server...
taskkill /F /FI "WINDOWTITLE eq Education ERP - Backend*" 2>nul
taskkill /F /IM php.exe 2>nul

REM Kill Node/Vite servers
echo Stopping Frontend Server...
taskkill /F /FI "WINDOWTITLE eq Education ERP - Frontend*" 2>nul
taskkill /F /IM node.exe 2>nul

echo.
echo ============================================
echo    All servers stopped!
echo ============================================
echo.

pause
