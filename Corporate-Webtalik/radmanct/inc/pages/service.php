<?php
namespace RadmanCT\Service;
defined('ABSPATH')||exit;
function data():array {
 return apply_filters('radmanct/service/mosaic',array('label'=>'','title'=>'','desc'=>'','items'=>array()));
}
function portfolio_summary():array {
 $projects=get_posts(array('post_type'=>'project','post_status'=>'publish','has_password'=>false,'posts_per_page'=>8,'no_found_rows'=>true));
 $categories=get_terms(array('taxonomy'=>'project_category','hide_empty'=>true,'fields'=>'count'));
 $services=array_filter(\RadmanCT\Services\items(),static fn(array $service):bool=>!empty($service['url']));
 $metrics=array(
  'نمونه‌کار منتشرشده'=>(int)wp_count_posts('project')->publish,
  'دستهٔ نمونه‌کار'=>is_wp_error($categories)?null:(int)$categories,
  'خدمت تخصصی'=>count($services),
 );
 return array('projects'=>$projects,'metrics'=>array_filter($metrics,static fn($value):bool=>null!==$value));
}
function allowed(int $id):bool {
 if(!$id||get_post_type($id)!=='attachment'||!in_array(get_post_status($id),array('inherit','publish'),true)||post_password_required($id))return false;
 $parent=wp_get_post_parent_id($id);
 return !$parent||(is_post_publicly_viewable($parent)&&!post_password_required($parent));
}
