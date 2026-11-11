@echo off
cd /d "%~dp0"
docker compose --env-file .env -f tools/compose.yaml up -d --wait
if errorlevel 1 goto failed
docker compose --env-file .env -f tools/compose.yaml exec -T wordpress php /opt/library/provision.php
if errorlevel 1 goto failed
echo http://127.0.0.1:8097
exit /b 0
:failed
echo Startup failed. Existing data was preserved.
pause
exit /b 1
