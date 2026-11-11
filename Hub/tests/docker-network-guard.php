<?php
// Test instrumentation only. Not shipped in either theme/plugin release.
if (PHP_SAPI === 'cli' && isset($_SERVER['SCRIPT_FILENAME']) && basename($_SERVER['SCRIPT_FILENAME']) === 'hub-smoke.php') {
    add_filter('pre_http_request', function($pre, $args, $url) {
        if (false !== $pre) { return $pre; }
        return new WP_Error('hub_test_no_network', 'Network disabled for bounded smoke checks');
    }, PHP_INT_MAX, 3);
}
