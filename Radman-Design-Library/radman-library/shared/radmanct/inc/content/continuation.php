<?php
namespace RadmanCT\Content\Continuation;
defined('ABSPATH')||exit;

function data():array {
 $current=get_post();
 if(!$current||!in_array($current->post_type,array('post','project'),true)||!is_post_publicly_viewable($current)||post_password_required($current))return array();
 $taxonomy=$current->post_type==='project'?'project_category':'category';
 // Core owns adjacency, date/ID ordering and caching. Limit suggestions to public, unprotected content even for signed-in editors.
 $public=static fn(string $where):string=>$where." AND p.post_status = 'publish' AND p.post_password = ''";
 add_filter('get_previous_post_where',$public);add_filter('get_next_post_where',$public);
 try{
  $previous=get_adjacent_post(false,'',true,$taxonomy);
  $next=get_adjacent_post(false,'',false,$taxonomy);
 }finally{
  remove_filter('get_previous_post_where',$public);remove_filter('get_next_post_where',$public);
 }
 $terms=wp_get_object_terms($current->ID,$taxonomy,array('fields'=>'ids'));
 $query=array(
  'post_type'=>$current->post_type,'post_status'=>'publish','has_password'=>false,
  'post__not_in'=>array($current->ID),'posts_per_page'=>3,'no_found_rows'=>true,'ignore_sticky_posts'=>true,
  'orderby'=>array('date'=>'DESC','ID'=>'DESC'),
 );
 $matched=!is_wp_error($terms)&&!empty($terms);
 if($matched)$query['tax_query']=array(array('taxonomy'=>$taxonomy,'field'=>'term_id','terms'=>$terms));
 $related=new \WP_Query($query);
 if($matched&&!$related->have_posts()){
  $matched=false;unset($query['tax_query']);$related=new \WP_Query($query);
 }
 return array('previous'=>$previous,'next'=>$next,'related'=>$related,'matched'=>$matched,'project'=>$current->post_type==='project');
}
