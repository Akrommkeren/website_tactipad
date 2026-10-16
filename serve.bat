@echo off
set "PHPRC=C:\Users\akmal\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe"
set "PATH=C:\Users\akmal\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe;%PATH%"
php "%~dp0artisan" serve %*
