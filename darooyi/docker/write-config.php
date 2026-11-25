<?php
$settings = [
    'DB_NAME' => 'darooyic_wpdb',
    'DB_USER' => 'darooyi',
    'DB_HOST' => 'db',
    'DB_CHARSET' => 'utf8mb4',
    'DB_COLLATE' => '',
    'WP_ENVIRONMENT_TYPE' => 'local',
    'WP_HTTP_BLOCK_EXTERNAL' => true,
    'DISABLE_WP_CRON' => true,
    'AUTOMATIC_UPDATER_DISABLED' => true,
    'WP_DEBUG_DISPLAY' => false,
    'WP_MEMORY_LIMIT' => '256M',
    'WP_MAX_MEMORY_LIMIT' => '512M',
];
foreach (['AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT'] as $key) {
    $settings[$key] = bin2hex(random_bytes(48));
}
$config = "<?php\n";
foreach ($settings as $key => $value) {
    $config .= 'define(' . var_export($key, true) . ', ' . var_export($value, true) . ");\n";
}
$config .= <<<'CONFIG'
define('DB_PASSWORD', getenv('DAROOYI_DB_PASSWORD'));
define('WP_HOME', getenv('DAROOYI_LOCAL_URL'));
define('WP_SITEURL', getenv('DAROOYI_LOCAL_URL'));
$table_prefix = 'cmp_';
require_once __DIR__ . '/wp-settings.php';
CONFIG;
if (file_put_contents('/var/www/html/wp-config.php', $config . "\n") === false) {
    exit(1);
}
chmod('/var/www/html/wp-config.php', 0640);
