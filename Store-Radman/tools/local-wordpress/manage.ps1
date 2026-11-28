param([ValidateSet('Start','Stop','Status')][string]$Action='Start')
$ErrorActionPreference='Stop'
$projectRoot=(Resolve-Path (Join-Path $PSScriptRoot '../..')).Path
$envFile=Join-Path $projectRoot '.env.local-wordpress'
$composeFile=Join-Path $PSScriptRoot 'compose.yaml'
$accessFile=Join-Path $projectRoot 'artifacts/local-wordpress-access.txt'
function Invoke-StoreCompose {
 param([string[]]$Arguments)
 & docker compose --project-name store-radman --env-file $envFile --file $composeFile @Arguments
 if($LASTEXITCODE -ne 0){throw "Store Radman command failed: $LASTEXITCODE"}
}
function New-StoreSecret {
 $bytes=New-Object byte[] 24
 $rng=[Security.Cryptography.RandomNumberGenerator]::Create()
 try{$rng.GetBytes($bytes)}finally{$rng.Dispose()}
 return ([BitConverter]::ToString($bytes)).Replace('-','').ToLowerInvariant()
}
try {
 & docker info --format '{{.ServerVersion}}' *> $null
 if($LASTEXITCODE -ne 0){throw 'Start Docker Desktop first.'}
 if(!(Test-Path -LiteralPath $envFile)){
  if($Action -ne 'Start'){throw 'Start the site first.'}
  $volumes=& docker volume ls --format '{{.Name}}'
  if($volumes -contains 'store-radman-database' -or $volumes -contains 'store-radman-wordpress'){throw 'Existing volumes found. Restore the original .env.local-wordpress; no data was reset.'}
  $storeDbPassword=New-StoreSecret
  $storeRootPassword=New-StoreSecret
  $storeAdminPassword=New-StoreSecret
  $config="STORE_DB_PASSWORD=$storeDbPassword`r`nSTORE_DB_ROOT_PASSWORD=$storeRootPassword`r`nSTORE_ADMIN_PASSWORD=$storeAdminPassword`r`n"
  [IO.File]::WriteAllText($envFile,$config,[Text.Encoding]::ASCII)
  New-Item -ItemType Directory -Path (Split-Path $accessFile) -Force | Out-Null
  [IO.File]::WriteAllText($accessFile,"Store Radman local`r`nURL: http://127.0.0.1:8100/`r`nAdmin: http://127.0.0.1:8100/wp-admin/`r`nUsername: radman`r`nInitial password: $storeAdminPassword`r`n",[Text.Encoding]::UTF8)
 }
 switch($Action){
  'Start' {
   Invoke-StoreCompose @('up','-d','--pull','never','--wait','--wait-timeout','60')
   foreach($script in @('install.php','plugins.php','configure.php','pages.php')){
    Invoke-StoreCompose @('exec','-T','--user','www-data','wordpress','php',"/opt/store-radman/$script")
   }
   Write-Host "Ready: http://127.0.0.1:8100/ Login details: $accessFile"
  }
  'Stop' {Invoke-StoreCompose @('stop');Write-Host 'Stopped; data preserved.'}
  'Status' {Invoke-StoreCompose @('ps','-a')}
 }
}catch{Write-Error $_;exit 1}
