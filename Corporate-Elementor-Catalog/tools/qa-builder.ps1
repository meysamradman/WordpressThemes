$ErrorActionPreference = 'Stop'
$qaRoot = Split-Path -Parent $PSScriptRoot
$qaId = [guid]::NewGuid().ToString('N').Substring(0, 10)
$qaNetwork = 'rdct-builder-qa-' + $qaId
$qaDatabase = 'rdct-builder-db-' + $qaId
try {
    docker network create --internal $qaNetwork | Out-Null
    if ($LASTEXITCODE -ne 0) { throw 'Could not create isolated network.' }
    docker run --pull=never -d --name $qaDatabase --network $qaNetwork --network-alias db --tmpfs '/var/lib/mysql:rw,size=256m' -e MARIADB_ALLOW_EMPTY_ROOT_PASSWORD=1 -e MARIADB_ROOT_HOST=% -e MARIADB_DATABASE=radman_qa mariadb:11.4 | Out-Null
    if ($LASTEXITCODE -ne 0) { throw 'Could not start isolated database.' }
    $qaScript = 'cp -a /usr/src/wordpress/. /var/www/html/ && php /workspace/tests/runtime/bootstrap.php && php /workspace/tests/runtime/builder.php'
    docker run --pull=never --rm --network $qaNetwork --tmpfs '/var/www/html:rw,size=512m' -e RDCT_ISOLATED_QA=1 --mount "type=bind,source=$qaRoot,target=/workspace,readonly" --mount "type=bind,source=$qaRoot/radmanct,target=/var/www/html/wp-content/themes/radmanct,readonly" --entrypoint sh wordpress:php8.4-apache -c $qaScript
    if ($LASTEXITCODE -ne 0) { throw 'Isolated builder check failed.' }
}
finally {
    docker rm -f $qaDatabase 2>$null | Out-Null
    docker network rm $qaNetwork 2>$null | Out-Null
}
