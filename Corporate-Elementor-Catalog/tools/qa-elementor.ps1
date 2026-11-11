param([switch]$RendererOnly, [switch]$ComponentsOnly, [switch]$EditorOnly, [switch]$StartupOnly, [switch]$ActivationOnly, [switch]$PreviewOnly)
$ErrorActionPreference = 'Stop'
$qaRoot = Split-Path -Parent $PSScriptRoot
$qaId = [guid]::NewGuid().ToString('N').Substring(0, 10)
$qaNetwork = 'rdct-qa-' + $qaId
$qaDatabase = 'rdct-db-' + $qaId
try {
    docker network create --internal $qaNetwork | Out-Null
    if ($LASTEXITCODE -ne 0) { throw 'Could not create isolated network.' }
    docker run --pull=never -d --name $qaDatabase --network $qaNetwork --network-alias db --tmpfs '/var/lib/mysql:rw,size=256m' -e MARIADB_ALLOW_EMPTY_ROOT_PASSWORD=1 -e MARIADB_ROOT_HOST=% -e MARIADB_DATABASE=radman_qa mariadb:11.4 | Out-Null
    if ($LASTEXITCODE -ne 0) { throw 'Could not start isolated database.' }
    $qaScript = 'cp -a /usr/src/wordpress/. /var/www/html/ && php /workspace/tests/runtime/bootstrap.php && php /workspace/tests/runtime/components-activate.php && php /workspace/tests/runtime/elementor.php && php /workspace/tests/runtime/elementor.php activate && php /workspace/tests/runtime/elementor.php && php /workspace/tests/runtime/elementor.php renderer-create && php /workspace/tests/runtime/elementor.php renderer'
    if ($RendererOnly) {
        $qaScript = 'cp -a /usr/src/wordpress/. /var/www/html/ && php /workspace/tests/runtime/bootstrap.php && php /workspace/tests/runtime/elementor.php activate && php /workspace/tests/runtime/elementor.php renderer-create && php /workspace/tests/runtime/elementor.php renderer'
    }
    if ($ComponentsOnly) {
        $qaScript = 'cp -a /usr/src/wordpress/. /var/www/html/ && php /workspace/tests/runtime/bootstrap.php && php /workspace/tests/runtime/components.php && php /workspace/tests/runtime/elementor.php activate && php /workspace/tests/runtime/components.php'
    }
    if ($ActivationOnly) {
        $qaScript = 'cp -a /usr/src/wordpress/. /var/www/html/ && php /workspace/tests/runtime/bootstrap.php && timeout 40 php /workspace/tests/runtime/components-activation.php elementor && timeout 40 php /workspace/tests/runtime/components-activation.php activate && timeout 40 php /workspace/tests/runtime/components-activation.php verify'
    }
    if ($PreviewOnly) {
        $qaScript = 'cp -a /usr/src/wordpress/. /var/www/html/ && php /workspace/tests/runtime/bootstrap.php && timeout 35 php /workspace/tests/runtime/components-activation.php elementor && timeout 35 php /workspace/tests/runtime/components-activation.php activate && timeout 40 php /workspace/tests/runtime/components-preview.php >/dev/null'
    }
    if ($EditorOnly) {
        $qaScript = 'cp -a /usr/src/wordpress/. /var/www/html/ && php /workspace/tests/runtime/bootstrap.php && php /workspace/tests/runtime/elementor.php activate && timeout 20 php /workspace/tests/runtime/elementor-editor.php'
    }
    if ($StartupOnly) {
        $qaScript = 'cp -a /usr/src/wordpress/. /var/www/html/ && php /workspace/tests/runtime/bootstrap.php && php /workspace/tests/runtime/elementor.php activate && timeout 40 php /workspace/tests/runtime/elementor-startup.php'
    }
    docker run --pull=never --rm --network $qaNetwork --tmpfs '/var/www/html:rw,size=512m' -e RDCT_ISOLATED_QA=1 --mount "type=bind,source=$qaRoot,target=/workspace,readonly" --mount "type=bind,source=$qaRoot/radmanct,target=/var/www/html/wp-content/themes/radmanct,readonly" --mount "type=bind,source=$qaRoot/radmanct-components,target=/var/www/html/wp-content/plugins/radmanct-components,readonly" --mount "type=bind,source=$qaRoot/themes/elementor,target=/var/www/html/wp-content/plugins/elementor,readonly" --mount "type=bind,source=$qaRoot/themes/elementor-pro,target=/var/www/html/wp-content/plugins/elementor-pro,readonly" --entrypoint sh wordpress:php8.4-apache -c $qaScript
    if ($LASTEXITCODE -ne 0) { throw 'Isolated Elementor smoke check failed.' }
}
finally {
    docker rm -f $qaDatabase 2>$null | Out-Null
    docker network rm $qaNetwork 2>$null | Out-Null
}
