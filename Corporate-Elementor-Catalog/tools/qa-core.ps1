param([switch]$McpOnly)
$ErrorActionPreference = 'Stop'
$qaRoot = Split-Path -Parent $PSScriptRoot
$qaId = [guid]::NewGuid().ToString('N').Substring(0, 10)
$qaNetwork = 'radmanct-content-qa-' + $qaId
$qaDatabase = 'radmanct-content-db-' + $qaId
try {
    docker network create --internal $qaNetwork | Out-Null
    if ($LASTEXITCODE -ne 0) { throw 'Could not create isolated network.' }
    docker run --pull=never -d --name $qaDatabase --network $qaNetwork --network-alias db --tmpfs '/var/lib/mysql:rw,size=256m' -e MARIADB_ALLOW_EMPTY_ROOT_PASSWORD=1 -e MARIADB_ROOT_HOST=% -e MARIADB_DATABASE=radman_qa mariadb:11.4 | Out-Null
    if ($LASTEXITCODE -ne 0) { throw 'Could not start isolated database.' }
    $qaScript = 'cp -a /usr/src/wordpress/. /var/www/html/ && php /workspace/tests/runtime/bootstrap.php && php /workspace/tests/runtime/core.php activate && php /workspace/tests/runtime/core.php && php /workspace/tests/runtime/core.php other-theme && php /workspace/tests/runtime/core.php portfolio-only && php /workspace/tests/runtime/core.php disabled'
    $qaExtraMounts = @()
    if ($McpOnly) {
        $qaExtraMounts = @('--mount', "type=bind,source=$qaRoot/themes/mcp-adapter,target=/var/www/html/wp-content/plugins/mcp-adapter,readonly", '--mount', "type=bind,source=$qaRoot/radmanct-mcp,target=/var/www/html/wp-content/plugins/radmanct-mcp,readonly")
        $qaScript = 'cp -a /usr/src/wordpress/. /var/www/html/ && php /workspace/tests/runtime/bootstrap.php && php /workspace/tests/runtime/core.php activate && timeout 15 php /workspace/tests/runtime/mcp.php prepare && timeout 15 php /workspace/tests/runtime/mcp.php read && timeout 15 php /workspace/tests/runtime/mcp.php && timeout 15 php /workspace/tests/runtime/mcp.php standalone-prepare && timeout 15 php /workspace/tests/runtime/mcp.php standalone'
    }
    docker run --pull=never --rm --network $qaNetwork --tmpfs '/var/www/html:rw,size=512m' -e RDCT_ISOLATED_QA=1 --mount "type=bind,source=$qaRoot,target=/workspace,readonly" --mount "type=bind,source=$qaRoot/radmanct,target=/var/www/html/wp-content/themes/radmanct,readonly" --mount "type=bind,source=$qaRoot/radmanct-content,target=/var/www/html/wp-content/plugins/radmanct-content,readonly" @qaExtraMounts --entrypoint sh wordpress:php8.4-apache -c $qaScript
    if ($LASTEXITCODE -ne 0) { throw 'Isolated Core check failed.' }
}
finally {
    docker rm -f $qaDatabase 2>$null | Out-Null
    docker network rm $qaNetwork 2>$null | Out-Null
}
