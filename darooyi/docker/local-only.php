<?php
/**
 * Plugin Name: Darooyi Docker isolation
 */
defined('ABSPATH') || exit;
add_filter('pre_wp_mail', '__return_false');
