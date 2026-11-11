<?php
namespace RadmanLibrary\Blog;
defined('ABSPATH')||exit;
require_once get_template_directory().'/shared/radmanct/inc/blog.php';
function native(?\WP_Query $query=null):bool {
    $query??=$GLOBALS['wp_query'];
    return $query->is_singular('post')||$query->is_category()||$query->is_tag()||$query->is_author()||$query->is_date()||(bool)$query->get('rdl_blog');
}
function rendering(?bool $value=null):bool {static $state=false;if($value!==null)$state=$value;return $state;}
function url():string {return \RadmanLibrary\Samples\link('blog-archive');}
function render(string $layout):void {
    \RadmanLibrary\DemoQuery\render('post',$layout,native(),static function()use($layout){rendering(true);try{require get_template_directory().'/shared/radmanct/templates/blog-'.$layout.'.php';}finally{rendering(false);}});
}
add_filter('query_vars',static function(array $vars):array{$vars[]='rdl_blog';return $vars;});
add_filter('get_search_form',static function(string $form):string{
    if(!native()&&!rendering())return $form;
    $id=wp_unique_id('rdc-srch-');
    return '<form class="rdc-srch" role="search" data-rdc-srch method="get" action="'.esc_url(home_url('/')).'"><label class="screen-reader-text" for="'.esc_attr($id).'">جستجوی نوشته‌ها</label><input id="'.esc_attr($id).'" type="search" name="s" value="'.esc_attr(get_search_query()).'" placeholder="جستجوی نوشته‌ها…"><input type="hidden" name="rdl_blog" value="1"><span aria-hidden="true">'.\RadmanCT\icon('search').'</span></form>';
});
