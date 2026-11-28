<?php
declare(strict_types=1);
namespace RadmanStore\Setup;
defined('ABSPATH') || exit;

add_action('after_setup_theme', static function (): void {
    load_theme_textdomain('radmanstore', get_template_directory().'/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script']);
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    register_nav_menus(['primary'=>__('فهرست اصلی','radmanstore')]);
});
