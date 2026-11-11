@echo off
cd /d "%~dp0"
docker compose --env-file .env -f tools/compose.yaml stop
