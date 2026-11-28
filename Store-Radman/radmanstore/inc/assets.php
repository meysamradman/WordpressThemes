<?php
declare(strict_types=1);
namespace RadmanStore\Assets;
defined('ABSPATH') || exit;

function style(string $name, array $dependencies=[]): void {
    $path='/assets/'.$name.'.css';
    wp_enqueue_style('radmanstore-'.$name,get_theme_file_uri($path),$dependencies,(string)filemtime(get_theme_file_path($path)));
}

add_action('wp_enqueue_scripts', static function (): void {
    style('tokens');
    style('base',['radmanstore-tokens']);
    $commerce=function_exists('is_woocommerce') && is_woocommerce();
    if (is_home() || (is_archive() && !$commerce) || is_search() || is_singular('post')) {
        style('blog',['radmanstore-base']);
    }
    wp_register_script('radmanstore-pagination',get_theme_file_uri('/assets/pagination.js'),[],(string)filemtime(get_theme_file_path('/assets/pagination.js')),['in_footer'=>true,'strategy'=>'defer']);
    if (is_home() || is_archive() || is_search()) {
        style('pagination',['radmanstore-base']);
    }
});

add_action('admin_enqueue_scripts', static function (): void {
    style('tokens');
    style('admin',['radmanstore-tokens']);
});
