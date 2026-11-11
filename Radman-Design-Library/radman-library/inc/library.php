<?php
namespace RadmanLibrary\Library;
defined('ABSPATH')||exit;
require_once get_template_directory().'/shared/radmanct/inc/ui/category-tree.php';
add_filter('request',static function(array $vars):array{if(!is_admin())unset($vars['rdl_kind'],$vars['rdl_fx']);return $vars;});
function filters():array {
    $out=[];foreach(['s','rdl_group'] as $key){$value=get_query_var($key);if(is_scalar($value)&&trim((string)$value)!=='')$out[$key]=(string)$value;}
    return $out;
}
function url(array $changes=[]):string {
    $args=array_merge(filters(),$changes);$args=array_filter($args,static fn($v)=>$v!=='');return add_query_arg($args,home_url('/'));
}
add_action('wp_enqueue_scripts',static function():void{
    if(\RadmanLibrary\Samples\raw()||is_singular('rdl_design'))return;
    $dir=get_template_directory();$uri=get_template_directory_uri();
    wp_enqueue_style('rdl-cat-tree',$uri.'/shared/radmanct/src/css/ui/category-tree.css',['rdl-shell'],filemtime($dir.'/shared/radmanct/src/css/ui/category-tree.css'));
    wp_enqueue_style('rdl-sidebar',$uri.'/shared/radmanct/src/css/ui/sidebar.css',['rdl-cat-tree'],filemtime($dir.'/shared/radmanct/src/css/ui/sidebar.css'));
    wp_enqueue_script('rdl-library',$uri.'/assets/library.js',[],filemtime($dir.'/assets/library.js'),['in_footer'=>true,'strategy'=>'defer']);
});
