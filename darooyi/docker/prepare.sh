#!/bin/bash
set -euo pipefail
cd /var/www/html
if [[ ! -f .darooyi-files-ready ]]; then
    if [[ -f /local-input/snapshot.tar ]]; then
        tar -xf /local-input/snapshot.tar
    else
        tar -C /snapshot \
          --exclude='./wp-config.php' --exclude='./.htaccess' --exclude='./aios-bootstrap.php' \
          --exclude='./wp-content/mu-plugins/aios-firewall-loader.php' \
          -cf - . | tar -xf -
    fi
    cp /setup/rewrite.conf .htaccess
    mkdir -p wp-content/mu-plugins
    cp /setup/local-only.php wp-content/mu-plugins/darooyi-local-only.php
    # This configuration is created only for the Docker copy.
    php /setup/write-config.php
    touch .darooyi-files-ready
    chown -R www-data:www-data /var/www/html
fi

wp() { php /local-input/wp-cli.phar --allow-root --skip-plugins --skip-themes --path=/var/www/html "$@"; }
if [[ "$(wp option get darooyi_local_prepared 2>/dev/null || true)" != 1 ]]; then
    test "$(wp option get template)" = woodmart
    for old in https://darooyi.com http://darooyi.com https://www.darooyi.com http://www.darooyi.com; do
        wp search-replace "$old" "$DAROOYI_LOCAL_URL" --all-tables-with-prefix --precise --skip-columns=guid --report-changed-only
    done
    # Elementor JSON can store escaped slashes inside serialized fields.
    wp search-replace 'https:\/\/darooyi.com' 'http:\/\/127.0.0.1:8099' --all-tables-with-prefix --precise --skip-columns=guid --report-changed-only
    wp option update darooyi_local_prepared 1
fi
