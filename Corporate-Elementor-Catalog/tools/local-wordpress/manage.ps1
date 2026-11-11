param(
    [ValidateSet('Start', 'Stop', 'Status')][string]$Action = 'Start',
    [switch]$OpenBrowser
)
$ErrorActionPreference = 'Stop'
$workspaceRoot = (Resolve-Path (Join-Path $PSScriptRoot '../..')).Path
$envFile = Join-Path $workspaceRoot '.env.local-wordpress'
$composeFile = Join-Path $PSScriptRoot 'compose.yaml'
$accessFile = Join-Path $workspaceRoot 'artifacts/local-wordpress-access.txt'
$siteUrl = 'http://127.0.0.1:8094'

function Invoke-LocalCompose {
    param([string[]]$Arguments)
    & docker compose --project-name radman-local --env-file $envFile --file $composeFile @Arguments
    if ($LASTEXITCODE -ne 0) { throw "Docker Compose failed (exit $LASTEXITCODE)." }
}

function New-LocalSecret {
    $bytes = New-Object byte[] 24
    $rng = [System.Security.Cryptography.RandomNumberGenerator]::Create()
    try { $rng.GetBytes($bytes) } finally { $rng.Dispose() }
    return ([BitConverter]::ToString($bytes)).Replace('-', '').ToLowerInvariant()
}

try {
    & docker info --format '{{.ServerVersion}}' *> $null
    if ($LASTEXITCODE -ne 0) {
        if ($Action -ne 'Start') { throw 'Docker Desktop is not running.' }
        $dockerDesktop = Join-Path $env:ProgramFiles 'Docker/Docker/Docker Desktop.exe'
        if (-not (Test-Path -LiteralPath $dockerDesktop)) { throw 'Start Docker Desktop, then run this file again.' }
        Start-Process -FilePath $dockerDesktop -WindowStyle Hidden
        Write-Host 'Waiting for Docker Desktop...'
        $ready = $false
        for ($attempt = 0; $attempt -lt 30; $attempt++) {
            Start-Sleep -Seconds 2
            & docker info --format '{{.ServerVersion}}' *> $null
            if ($LASTEXITCODE -eq 0) { $ready = $true; break }
        }
        if (-not $ready) { throw 'Docker is still starting. Run WordPress-Start.cmd again when Docker Desktop is ready.' }
    }

    if (-not (Test-Path -LiteralPath $envFile)) {
        if ($Action -ne 'Start') { throw 'No local site configuration yet. Run WordPress-Start.cmd first.' }
        $existingDatabase = & docker volume ls --filter name=radman-local-database --format '{{.Name}}'
        if ($existingDatabase -contains 'radman-local-database') { throw 'The database volume exists but its .env.local-wordpress file is missing. Restore that file; do not generate new credentials.' }
        $dbPassword = New-LocalSecret
        $dbRootPassword = New-LocalSecret
        $adminPassword = New-LocalSecret
        $configuration = "RADMAN_DB_PASSWORD=$dbPassword`r`nRADMAN_DB_ROOT_PASSWORD=$dbRootPassword`r`nRADMAN_ADMIN_PASSWORD=$adminPassword`r`n"
        [System.IO.File]::WriteAllText($envFile, $configuration, [System.Text.Encoding]::ASCII)
        New-Item -ItemType Directory -Path (Split-Path $accessFile) -Force | Out-Null
        $access = "Radman CT shared local WordPress`r`n`r`nWebsite: $siteUrl/`r`nAdmin: $siteUrl/wp-admin/`r`nUsername: radman`r`nInitial password: $adminPassword`r`n`r`nCredentials are local and excluded from Git. If you change the password in WordPress, this initial password is no longer current.`r`n"
        [System.IO.File]::WriteAllText($accessFile, $access, [System.Text.Encoding]::UTF8)
    }

    switch ($Action) {
        'Start' {
            Invoke-LocalCompose -Arguments @('up', '-d', '--pull', 'never', '--wait', '--wait-timeout', '60')
            Invoke-LocalCompose -Arguments @('exec', '-T', 'wordpress', 'php', '/opt/radman/provision.php')
            Write-Host "`nWebsite: $siteUrl/"
            Write-Host "Admin: $siteUrl/wp-admin/"
            Write-Host "Login details: $accessFile"
            if ($OpenBrowser) { Start-Process "$siteUrl/wp-admin/" }
        }
        'Stop' {
            Invoke-LocalCompose -Arguments @('stop')
            Write-Host 'Stopped. Database, media and site settings are preserved.'
        }
        'Status' { Invoke-LocalCompose -Arguments @('ps', '-a') }
    }
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
