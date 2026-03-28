@echo off
set PHP=C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe
set DIR=C:\laragon\www\syntropic-rx

echo Starting Syntropic Rx...
start "PHP Server" cmd /k "%PHP% %DIR%\artisan serve"
timeout /t 2 /nobreak >nul
start "Vite" cmd /k "cd /d %DIR% && npm run dev"
echo Open: http://127.0.0.1:8000
