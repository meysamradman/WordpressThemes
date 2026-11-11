$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.IO.Compression.FileSystem
Add-Type -AssemblyName System.IO.Compression
$bundleRoot = Split-Path -Parent $PSScriptRoot
foreach ($bundleSlug in @('radmanct-content', 'radmanct-mcp', 'radmanct-components')) {
$bundleSource = Join-Path $bundleRoot $bundleSlug
$bundleTarget = Join-Path $bundleRoot 'radmanct/bundled-plugins'
New-Item -ItemType Directory -Path $bundleTarget -Force | Out-Null
$bundleArchive = Join-Path $bundleTarget ($bundleSlug + '.zip')
$bundleTemporary = Join-Path $bundleTarget ('plugin-' + [guid]::NewGuid().ToString('N') + '.tmp')
$bundleVersionMatch = [regex]::Match([IO.File]::ReadAllText((Join-Path $bundleSource ($bundleSlug + '.php'))), '(?m)^\s*\* Version:\s*([0-9A-Za-z.+-]+)\s*$')
if (-not $bundleVersionMatch.Success) { throw 'Plugin version header is missing.' }
try {
    $bundleZip = [IO.Compression.ZipFile]::Open($bundleTemporary, [IO.Compression.ZipArchiveMode]::Create)
    try {
        Get-ChildItem -LiteralPath $bundleSource -Recurse -File | ForEach-Object {
            $bundleRelative = $_.FullName.Substring($bundleSource.Length + 1).Replace('\', '/')
            if (($bundleRelative -eq ($bundleSlug + '.php') -or $bundleRelative -eq 'wpml-config.xml') -or $bundleRelative -match '^(inc|dist|languages|images|licenses)/') {
                [IO.Compression.ZipFileExtensions]::CreateEntryFromFile($bundleZip, $_.FullName, ($bundleSlug + '/' + $bundleRelative), [IO.Compression.CompressionLevel]::Optimal) | Out-Null
            }
        }
    }
    finally { $bundleZip.Dispose() }
    Move-Item -LiteralPath $bundleTemporary -Destination $bundleArchive -Force
    $bundleManifest = @{ version = $bundleVersionMatch.Groups[1].Value; sha256 = (Get-FileHash -LiteralPath $bundleArchive -Algorithm SHA256).Hash.ToLowerInvariant() } | ConvertTo-Json
    [IO.File]::WriteAllText((Join-Path $bundleTarget ($bundleSlug + '.json')), $bundleManifest, (New-Object Text.UTF8Encoding($false)))
    Write-Output ('Created local package: ' + $bundleSlug)
}
finally {
    if (Test-Path -LiteralPath $bundleTemporary) { Remove-Item -LiteralPath $bundleTemporary }
}

}

& (Join-Path $PSScriptRoot 'verify-packages.ps1') -SourceRoot $bundleRoot
