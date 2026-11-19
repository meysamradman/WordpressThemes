<?php
namespace RadmanCT\Services;
defined('ABSPATH')||exit;
function is_detail():bool {
    $slugs=array_column(apply_filters('radmanct/services/specs',array()),'slug');
    return $slugs&&is_page($slugs);
}
function has_portfolio_summary():bool {
    return is_page('custom-website-design-development');
}
add_filter('template_include',static function(string $template):string {
    return is_detail()?get_template_directory().'/templates/service.php':$template;
});
function items():array {
    static $items;
    if(null!==$items)return $items;
    $specs=apply_filters('radmanct/services/specs',array());
    if(!$specs)return $items=array();
    $pages=get_posts(array('post_type'=>'page','post_status'=>'publish','has_password'=>false,'post_name__in'=>array_column($specs,'slug'),'posts_per_page'=>count($specs),'no_found_rows'=>true,'update_post_term_cache'=>false,'update_post_meta_cache'=>false));
    $by_slug=array();foreach($pages as $page)$by_slug[$page->post_name]=$page;
    foreach($specs as &$item){
        $page=$by_slug[$item['slug']]??null;
        $item['title']=$page?$page->post_title:$item['title'];
        $item['description']=$page&&$page->post_excerpt?wp_strip_all_tags($page->post_excerpt):$item['description'];
        $item['url']=$page?get_permalink($page):'';
        $item['image']=\RadmanCT\media_id($item['media']);
    }unset($item);
    return $items=$specs;
}
