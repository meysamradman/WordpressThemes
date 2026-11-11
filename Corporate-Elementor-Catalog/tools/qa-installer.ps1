$ErrorActionPreference = 'Stop'
$installerQaRoot = Split-Path -Parent $PSScriptRoot
$installerQaId = [guid]::NewGuid().ToString('N').Substring(0, 10)
$installerQaNetwork = 'rdct-installer-qa-' + $installerQaId
$installerQaDatabase = 'rdct-installer-db-' + $installerQaId
try {
    docker network create --internal $installerQaNetwork | Out-Null
    if ($LASTEXITCODE -ne 0) { throw 'Could not create isolated network.' }
    docker run --pull=never -d --name $installerQaDatabase --network $installerQaNetwork --network-alias db --tmpfs '/var/lib/mysql:rw,size=512m' -e MARIADB_ALLOW_EMPTY_ROOT_PASSWORD=1 -e MARIADB_ROOT_HOST=% -e MARIADB_DATABASE=radman_qa mariadb:11.4 | Out-Null
    if ($LASTEXITCODE -ne 0) { throw 'Could not start isolated database.' }
    $installerQaScript = 'cp -a /usr/src/wordpress/. /var/www/html/ && php /workspace/tests/runtime/bootstrap.php && php /workspace/tests/runtime/installer.php'
    docker run --pull=never --rm --network $installerQaNetwork --tmpfs '/var/www/html:rw,size=512m' -e RDCT_ISOLATED_QA=1 --mount "type=bind,source=$installerQaRoot,target=/workspace,readonly" --mount "type=bind,source=$installerQaRoot/radmanct,target=/var/www/html/wp-content/themes/radmanct,readonly" --entrypoint sh wordpress:php8.4-apache -c $installerQaScript
    if ($LASTEXITCODE -ne 0) { docker logs --tail 12 $installerQaDatabase; throw 'Isolated plugin installer check failed.' }
}
finally {
    docker rm -f $installerQaDatabase 2>$null | Out-Null
    docker network rm $installerQaNetwork 2>$null | Out-Null
}
