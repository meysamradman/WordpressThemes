<?php
/**
 * Plugin Name: Webtalik Local Development
 */
defined( 'ABSPATH' ) || exit;
if ( '1' !== getenv( 'WEBTALIK_LOCAL_SITE' ) || 'local' !== wp_get_environment_type() ) { return; }
if ( ! defined( 'WP_ACCESSIBLE_HOSTS' ) ) { define( 'WP_ACCESSIBLE_HOSTS', 'api.wordpress.org,downloads.wordpress.org' ); }

add_filter( 'pre_wp_mail', '__return_false' );
add_filter( 'automatic_updater_disabled', '__return_true' );
add_filter( 'pre_http_request', static function ( $result, $args, $url ) {
    $host = wp_parse_url( $url, PHP_URL_HOST );
    if ( in_array( $host, array( 'localhost', '127.0.0.1', 'wordpress' ), true ) ) { return $result; }
    if ( 'https' === wp_parse_url( $url, PHP_URL_SCHEME ) && in_array( $host, array( 'api.wordpress.org', 'downloads.wordpress.org' ), true ) ) { return $result; }
    return new WP_Error( 'webtalik_local_offline', 'External HTTP requests are disabled on the Webtalik local site.' );
}, 10, 3 );
