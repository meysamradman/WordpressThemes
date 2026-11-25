<?php
/**
 * Plugin Name: Darooyi Docker isolation
 */
defined('ABSPATH') || exit;
add_filter('pre_wp_mail', '__return_false');

// The local repository relay accepts only official plugin requests.
define('WP_ACCESSIBLE_HOSTS', 'plugin-repository');
add_filter('pre_http_request', static function ($response, array $args, string $url) {
    if ($response !== false) {
        return $response;
    }

    $origin = wp_parse_url($url);
    $routes = [
        'api.wordpress.org' => 'api',
        'downloads.wordpress.org' => 'downloads',
    ];
    $route = $routes[$origin['host'] ?? ''] ?? null;
    if (!$route || ($origin['scheme'] ?? '') !== 'https'
        || isset($origin['user']) || isset($origin['pass']) || isset($origin['port'])) {
        return $response;
    }

    $path = $origin['path'] ?? '/';
    if (!preg_match('~^/(?:plugins|plugin|translation/plugin)/~D', $path)) {
        return $response;
    }
    $query = isset($origin['query']) ? '?' . $origin['query'] : '';
    $args['reject_unsafe_urls'] = false; // The destination is a fixed Docker service.
    return wp_remote_request("http://plugin-repository:8081/$route$path$query", $args);
}, 10, 3);
