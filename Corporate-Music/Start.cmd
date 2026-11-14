@echo off
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0tools\local-wordpress\manage.ps1" -Action Start
