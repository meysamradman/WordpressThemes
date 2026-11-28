<?php
declare(strict_types=1);
namespace RadmanStore\Commerce;
defined('ABSPATH') || exit;

function uses_assets(): bool {
    $required=function_exists('is_woocommerce') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page());
    if (!$required && is_singular()) {
        $post=get_post();
        $content=$post instanceof \WP_Post ? $post->post_content : '';
        $required=(bool)preg_match('/<!--\s+wp:woocommerce\/|\[(?:products?|product_page|product_category|product_categories|add_to_cart|add_to_cart_url|woocommerce_cart|woocommerce_checkout|woocommerce_my_account|recent_products|sale_products|best_selling_products|top_rated_products|featured_products|related_products)\b/', $content);
    }
    return (bool)apply_filters('radmanstore/commerce/assets',$required);
}

add_filter('loop_shop_per_page', static fn() => 8);
add_filter('woocommerce_enqueue_styles', static fn($styles) => uses_assets() ? $styles : []);
add_action('wp_enqueue_scripts', static function (): void {
    if (uses_assets()) { return; }
    foreach (['wc-add-to-cart','woocommerce','wc-cart-fragments'] as $handle) {
        wp_dequeue_script($handle);
    }
},100);

add_action('init', static function (): void {
    remove_action('woocommerce_before_main_content','woocommerce_output_content_wrapper',10);
    remove_action('woocommerce_after_main_content','woocommerce_output_content_wrapper_end',10);
    remove_action('woocommerce_sidebar','woocommerce_get_sidebar',10);
    add_action('woocommerce_before_main_content',static function (): void { echo '<main id="main-content" class="rds-main">'; },10);
    add_action('woocommerce_after_main_content',static function (): void { echo '</main>'; },10);
    remove_action('woocommerce_after_shop_loop','woocommerce_pagination',10);
    add_action('woocommerce_after_shop_loop',static function (): void {
        \RadmanStore\Pagination\render(['mode'=>'more','total'=>wc_get_loop_prop('total_pages'),'current'=>wc_get_loop_prop('current_page')]);
    },10);
});

add_filter('woocommerce_product_loop_start',static function (string $html): string {
    if ((is_shop() || is_product_taxonomy()) && !wc_get_loop_prop('name')) {
        return str_replace('<ul ','<ul data-rds-items ',$html);
    }
    return $html;
});

add_filter('woocommerce_structured_data_breadcrumblist',static fn($data) => defined('WPSEO_VERSION') ? [] : $data);
add_filter('woocommerce_allow_marketplace_suggestions','__return_false');
add_filter('woocommerce_allow_tracking','__return_false');
