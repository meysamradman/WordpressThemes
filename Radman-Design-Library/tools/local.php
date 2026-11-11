<?php
/** Plugin Name: Radman Library Local Boundaries */
defined('ABSPATH')||exit;
if('local'!==wp_get_environment_type())return;
add_filter('pre_wp_mail','__return_false');
add_filter('pre_http_request',static function($result,$args,$url){
    return new WP_Error('rdl_offline','External HTTP disabled in this local workshop.');
},10,3);
