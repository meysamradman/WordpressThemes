<?php
declare(strict_types=1);
namespace RadmanStore;
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
add_action('wp_enqueue_scripts', static function (): void {
    $file=get_theme_file_path('/assets/base.css');
    wp_enqueue_style('radmanstore-base',get_theme_file_uri('/assets/base.css'),[],(string)filemtime($file));
});
add_action('init', static function (): void {
    remove_action('woocommerce_before_main_content','woocommerce_output_content_wrapper',10);
    remove_action('woocommerce_after_main_content','woocommerce_output_content_wrapper_end',10);
    remove_action('woocommerce_sidebar','woocommerce_get_sidebar',10);
    add_action('woocommerce_before_main_content',static function (): void { echo '<main id="main-content" class="rds-main">'; },10);
    add_action('woocommerce_after_main_content',static function (): void { echo '</main>'; },10);
});
