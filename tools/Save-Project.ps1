[CmdletBinding()]
param(
    [Parameter(Mandatory)][ValidateNotNullOrEmpty()][string]$Project,
    [string]$Message,
    [switch]$Push,
    [switch]$CheckOnly
)
$ErrorActionPreference = 'Stop'
$repoRoot = [IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
if ($Project -in @('.', '..', 'tools', 'docs') -or $Project.StartsWith('.') -or $Project -match '[/\\]') { throw 'Choose one immediate project folder.' }
$target = Get-Item -LiteralPath (Join-Path $repoRoot $Project)
if (-not $target.PSIsContainer -or $target.Parent.FullName -ne $repoRoot -or ($target.Attributes -band [IO.FileAttributes]::ReparsePoint)) { throw 'The project must be a real immediate folder in this repository.' }
$scope = $target.Name + '/'
$pathspec = ':(literal)' + $scope
if (Test-Path -LiteralPath (Join-Path $target.FullName '.git')) { throw 'Nested Git is not permitted in a project folder.' }
function Invoke-RepoGit {
    & git -C $repoRoot -c core.quotepath=false @args
    if ($LASTEXITCODE -ne 0) { throw 'Git command failed; no reset or force operation was attempted.' }
}
$actualRoot = (Invoke-RepoGit rev-parse --show-toplevel).Trim()
if ([IO.Path]::GetFullPath($actualRoot) -ne $repoRoot) { throw 'Run this helper from its parent repository; its Git root does not match.' }
if ($CheckOnly) { Invoke-RepoGit status --short -- $pathspec; return }
if ([string]::IsNullOrWhiteSpace($Message)) { throw 'Provide -Message, or use -CheckOnly.' }
$staged = @(Invoke-RepoGit diff --cached --name-only)
if (@($staged | Where-Object { -not $_.StartsWith($scope, [StringComparison]::Ordinal) }).Count) { throw 'Other paths are staged. Preserve them and finish that work separately before saving this project.' }
$branch = (Invoke-RepoGit symbolic-ref --short HEAD).Trim()
if ($Push) {
    $remote = (Invoke-RepoGit remote get-url origin).Trim()
    if ($remote -ne 'https://github.com/meysamradman/WordpressThemes.git') { throw 'Origin does not match the authorized WordpressThemes repository.' }
    Invoke-RepoGit fetch origin
    & git -C $repoRoot show-ref --verify --quiet ('refs/remotes/origin/' + $branch)
    $base = if ($LASTEXITCODE -eq 0) { 'origin/' + $branch } else { 'origin/main' }
    $pending = @(Invoke-RepoGit rev-list ($base + '..HEAD'))
    foreach ($commit in $pending) {
        $files = @(Invoke-RepoGit diff-tree --root --no-commit-id --name-only -r -m $commit)
        if (@($files | Where-Object { -not $_.StartsWith($scope, [StringComparison]::Ordinal) }).Count) { throw 'Unpublished commits include other project paths; refusing a mixed-project push.' }
    }
}
Invoke-RepoGit add --all -- $pathspec
& git -C $repoRoot diff --cached --quiet -- $pathspec
if ($LASTEXITCODE -eq 1) { Invoke-RepoGit commit -m $Message } elseif ($LASTEXITCODE -ne 0) { throw 'Unable to inspect staged changes.' } else { Write-Output 'No changes in this project.' }
if ($Push) { Invoke-RepoGit push --set-upstream origin HEAD }
