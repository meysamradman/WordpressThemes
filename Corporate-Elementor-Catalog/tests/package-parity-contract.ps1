$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.IO.Compression.FileSystem
Add-Type -AssemblyName System.IO.Compression
$rdctFixture = Join-Path ([IO.Path]::GetTempPath()) ('rdct-parity-' + [guid]::NewGuid().ToString('N'))
$rdctVerifier = Join-Path (Split-Path -Parent $PSScriptRoot) 'tools/verify-packages.ps1'
New-Item -ItemType Directory -Path (Join-Path $rdctFixture 'radmanct/bundled-plugins') -Force | Out-Null
try {
    foreach ($rdctSlug in @('radmanct-content', 'radmanct-mcp', 'radmanct-components')) {
        $rdctStage = Join-Path $rdctFixture ('stage-' + $rdctSlug)
        $rdctSource = Join-Path $rdctStage $rdctSlug
        New-Item -ItemType Directory -Path (Join-Path $rdctSource 'inc') -Force | Out-Null
        [IO.File]::WriteAllText((Join-Path $rdctSource ($rdctSlug + '.php')), "<?php`n/*`n * Version: 0.1.0`n */`nconst VERSION = '0.1.0';`n")
        [IO.File]::WriteAllText((Join-Path $rdctSource 'inc/test.php'), '<?php return true;')
        Copy-Item -LiteralPath $rdctSource -Destination (Join-Path $rdctFixture $rdctSlug) -Recurse
        $rdctZip = Join-Path $rdctFixture ('radmanct/bundled-plugins/' + $rdctSlug + '.zip')
        $rdctArchive = [IO.Compression.ZipFile]::Open($rdctZip, [IO.Compression.ZipArchiveMode]::Create)
        try {
            Get-ChildItem -LiteralPath $rdctSource -Recurse -File | ForEach-Object {
                $rdctRelative = $_.FullName.Substring($rdctStage.Length + 1).Replace('\', '/')
                [IO.Compression.ZipFileExtensions]::CreateEntryFromFile($rdctArchive, $_.FullName, $rdctRelative) | Out-Null
            }
        } finally { $rdctArchive.Dispose() }
        @{version='0.1.0';sha256=(Get-FileHash -LiteralPath $rdctZip -Algorithm SHA256).Hash.ToLowerInvariant()} | ConvertTo-Json | Set-Content -LiteralPath ($rdctZip -replace '\.zip$', '.json') -Encoding UTF8
    }
    & $rdctVerifier -SourceRoot $rdctFixture
    [IO.File]::WriteAllText((Join-Path $rdctFixture 'radmanct-components/inc/test.php'), '<?php return false;')
    $rdctRejected = $false
    try { & $rdctVerifier -SourceRoot $rdctFixture | Out-Null } catch { $rdctRejected = $_.Exception.Message -like 'Package differs from source:*' }
    if (-not $rdctRejected) { throw 'A valid ZIP checksum hid a source mismatch.' }
    [IO.File]::WriteAllText((Join-Path $rdctFixture 'radmanct-components/inc/test.php'), '<?php return true;')
    $rdctHeader = Join-Path $rdctFixture 'radmanct-content/radmanct-content.php'
    [IO.File]::WriteAllText($rdctHeader, ([IO.File]::ReadAllText($rdctHeader).Replace("const VERSION = '0.1.0'", "const VERSION = '0.0.9'")))
    $rdctRejected = $false
    try { & $rdctVerifier -SourceRoot $rdctFixture | Out-Null } catch { $rdctRejected = $_.Exception.Message -like 'Version mismatch:*' }
    if (-not $rdctRejected) { throw 'Header and constant mismatch was accepted.' }
    Write-Output 'Passed: package source parity rejects stale bytes despite valid checksum and rejects inconsistent versions.'
}
finally {
    $rdctAbsolute = [IO.Path]::GetFullPath($rdctFixture)
    if ($rdctAbsolute.StartsWith([IO.Path]::GetFullPath([IO.Path]::GetTempPath()), [StringComparison]::OrdinalIgnoreCase) -and (Split-Path $rdctAbsolute -Leaf).StartsWith('rdct-parity-')) { Remove-Item -LiteralPath $rdctAbsolute -Recurse -Force }
}
