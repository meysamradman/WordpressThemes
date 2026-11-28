param([string]$MkcertPath)
$ErrorActionPreference='Stop'
$projectRoot=(Resolve-Path (Join-Path $PSScriptRoot '../..')).Path
$tlsRoot=Join-Path $projectRoot '.cache/local-tls'
if(!$MkcertPath){$MkcertPath=Join-Path $projectRoot '.cache/mkcert.exe'}
if(!(Test-Path -LiteralPath $MkcertPath)){throw 'Install official mkcert or pass -MkcertPath. See docs/local-https.md.'}
New-Item -ItemType Directory -Path "$tlsRoot/ca","$tlsRoot/server" -Force | Out-Null
$previousCaRoot=$env:CAROOT
try {
 $env:CAROOT=Join-Path $tlsRoot 'ca'
 & $MkcertPath -cert-file "$tlsRoot/server/cert.pem" -key-file "$tlsRoot/server/key.pem" localhost 127.0.0.1 ::1
 if($LASTEXITCODE -ne 0){throw 'Local certificate generation failed.'}
 Import-Certificate -FilePath "$tlsRoot/ca/rootCA.pem" -CertStoreLocation Cert:/CurrentUser/Root | Out-Null
} finally {
 if($null -eq $previousCaRoot){Remove-Item Env:CAROOT -ErrorAction SilentlyContinue}else{$env:CAROOT=$previousCaRoot}
}
Write-Host 'Local certificate trusted for this Windows user. Start Store Radman to load it.'
