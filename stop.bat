@echo off
title Education ERP - Stopping...

echo.
echo ============================================
echo    Education ERP - Stopping
echo ============================================
echo.

REM Kill PHP servers
echo Stopping PHP Server...
taskkill /F /FI "WINDOWTITLE eq Education ERP*" 2>nul
taskkill /F /IM php.exe 2>nul

echo.
echo ============================================
echo    Education ERP stopped!
echo ============================================
echo.

pause
