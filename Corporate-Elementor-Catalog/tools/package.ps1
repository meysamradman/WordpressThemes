param(
    [ValidateSet('radmanct', 'radmanct-child', 'radmanct-content', 'radmanct-mcp', 'radmanct-components')]
    [string[]]$Packages = @('radmanct', 'radmanct-child', 'radmanct-content', 'radmanct-mcp', 'radmanct-components')
)
$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.IO.Compression.FileSystem
$rdctRoot = Split-Path -Parent $PSScriptRoot
& (Join-Path $PSScriptRoot 'bundle-content.ps1')
$rdctArtifacts = Join-Path $rdctRoot 'artifacts'
New-Item -ItemType Directory -Path $rdctArtifacts -Force | Out-Null
$rdctStage = Join-Path ([IO.Path]::GetTempPath()) ('rdct-package-' + [guid]::NewGuid().ToString('N'))
New-Item -ItemType Directory -Path $rdctStage | Out-Null
try {
    foreach ($rdctTheme in $Packages) {
        $rdctSource = Join-Path $rdctRoot $rdctTheme
        $rdctHeaderFile = if ($rdctTheme -in @('radmanct-content', 'radmanct-mcp', 'radmanct-components')) { "$rdctTheme.php" } else { 'style.css' }
        $rdctHeader = [IO.File]::ReadAllText((Join-Path $rdctSource $rdctHeaderFile))
        $rdctVersionMatch = [regex]::Match($rdctHeader, '(?m)^[\t /*#@]*Version:[\t ]*([0-9A-Za-z.+-]{1,40})[\t ]*\r?$')
        if (-not $rdctVersionMatch.Success) { throw "Invalid version header: $rdctTheme" }
        $rdctDestination = Join-Path $rdctStage $rdctTheme
        New-Item -ItemType Directory -Path $rdctDestination | Out-Null
        Get-ChildItem -LiteralPath $rdctSource -Force | Where-Object { $_.Name -ne 'src' } | ForEach-Object {
            Copy-Item -LiteralPath $_.FullName -Destination $rdctDestination -Recurse
        }
        $rdctZip = Join-Path $rdctArtifacts ($rdctTheme + '-' + $rdctVersionMatch.Groups[1].Value + '.zip')
        $rdctTemporaryZip = $rdctZip + '.' + [guid]::NewGuid().ToString('N') + '.tmp'
        try {
            $rdctArchive = [IO.Compression.ZipFile]::Open($rdctTemporaryZip, [IO.Compression.ZipArchiveMode]::Create)
            try {
                Get-ChildItem -LiteralPath $rdctDestination -Recurse -File -Force | ForEach-Object {
                    $rdctEntry = $_.FullName.Substring($rdctStage.Length + 1).Replace('\', '/')
                    [IO.Compression.ZipFileExtensions]::CreateEntryFromFile($rdctArchive, $_.FullName, $rdctEntry, [IO.Compression.CompressionLevel]::Optimal) | Out-Null
                }
            }
            finally { $rdctArchive.Dispose() }
            Move-Item -LiteralPath $rdctTemporaryZip -Destination $rdctZip -Force
        }
        finally {
            if (Test-Path -LiteralPath $rdctTemporaryZip) { Remove-Item -LiteralPath $rdctTemporaryZip }
        }
        Write-Output $rdctZip
    }
}
finally {
    $rdctResolvedStage = [IO.Path]::GetFullPath($rdctStage)
    $rdctTempRoot = [IO.Path]::GetFullPath([IO.Path]::GetTempPath())
    if ($rdctResolvedStage.StartsWith($rdctTempRoot, [StringComparison]::OrdinalIgnoreCase) -and (Split-Path $rdctResolvedStage -Leaf).StartsWith('rdct-package-')) {
        Remove-Item -LiteralPath $rdctResolvedStage -Recurse -Force
    }
}
