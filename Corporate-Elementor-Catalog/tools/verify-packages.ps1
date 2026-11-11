param([string]$SourceRoot = (Split-Path -Parent $PSScriptRoot))
$ErrorActionPreference = 'Stop'
$SourceRoot = [IO.Path]::GetFullPath($SourceRoot)
Add-Type -AssemblyName System.IO.Compression.FileSystem
foreach ($rdctSlug in @('radmanct-content', 'radmanct-mcp', 'radmanct-components')) {
    $rdctSource = Join-Path $SourceRoot $rdctSlug
    $rdctBundle = Join-Path $SourceRoot ('radmanct/bundled-plugins/' + $rdctSlug)
    $rdctManifest = Get-Content -LiteralPath ($rdctBundle + '.json') -Raw | ConvertFrom-Json
    $rdctHeader = [IO.File]::ReadAllText((Join-Path $rdctSource ($rdctSlug + '.php')))
    $rdctVersion = [regex]::Match($rdctHeader, '(?m)^\s*\* Version:\s*([0-9A-Za-z.+-]+)\s*$').Groups[1].Value
    $rdctConstant = [regex]::Match($rdctHeader, "const VERSION = '([^']+)';")
    if (-not $rdctVersion -or $rdctManifest.version -ne $rdctVersion -or ($rdctConstant.Success -and $rdctConstant.Groups[1].Value -ne $rdctVersion)) { throw "Version mismatch: $rdctSlug" }
    if ((Get-FileHash -LiteralPath ($rdctBundle + '.zip') -Algorithm SHA256).Hash.ToLowerInvariant() -ne $rdctManifest.sha256) { throw "Checksum mismatch: $rdctSlug" }
    $rdctExpected = New-Object 'System.Collections.Generic.Dictionary[string,string]' ([StringComparer]::Ordinal)
    Get-ChildItem -LiteralPath $rdctSource -Recurse -File | ForEach-Object {
        $rdctRelative = $_.FullName.Substring($rdctSource.Length + 1).Replace('\', '/')
        if (($rdctRelative -eq ($rdctSlug + '.php') -or $rdctRelative -eq 'wpml-config.xml') -or $rdctRelative -match '^(inc|dist|languages|images|licenses)/') { $rdctExpected[$rdctSlug + '/' + $rdctRelative] = $_.FullName }
    }
    $rdctZip = [IO.Compression.ZipFile]::OpenRead($rdctBundle + '.zip')
    $rdctHasher = [Security.Cryptography.SHA256]::Create()
    try {
        foreach ($rdctEntry in $rdctZip.Entries) {
            if (-not $rdctExpected.ContainsKey($rdctEntry.FullName)) { throw "Unexpected or duplicate package file: $($rdctEntry.FullName)" }
            $rdctStream = $rdctEntry.Open()
            try { $rdctHash = [BitConverter]::ToString($rdctHasher.ComputeHash($rdctStream)).Replace('-', '') } finally { $rdctStream.Dispose() }
            if ($rdctHash -ne (Get-FileHash -LiteralPath $rdctExpected[$rdctEntry.FullName] -Algorithm SHA256).Hash) { throw "Package differs from source: $($rdctEntry.FullName)" }
            [void]$rdctExpected.Remove($rdctEntry.FullName)
        }
        if ($rdctExpected.Count) { throw "Missing package files: $rdctSlug" }
    }
    finally { $rdctHasher.Dispose(); $rdctZip.Dispose() }
    Write-Output ("Verified source, version and checksum: " + $rdctSlug)
}
