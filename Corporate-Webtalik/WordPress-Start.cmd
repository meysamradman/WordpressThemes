@echo off
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0tools\local-wordpress\manage.ps1" -Action Start
if errorlevel 1 pause
