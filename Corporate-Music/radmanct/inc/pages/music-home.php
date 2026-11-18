<?php
namespace RadmanCT\MusicHome;
defined('ABSPATH')||exit;
function data():array {static $data;return $data??=apply_filters('radmanct/music-home/data',array('label'=>'جدیدترین آثار','title'=>get_bloginfo('name'),'desc'=>get_bloginfo('description'),'tracks'=>'آخرین موزیک‌ها'));}
function entries(string $type,int $limit):array {
 $query=['post_type'=>$type==='track'?'track':'post','post_status'=>'publish','has_password'=>false,'posts_per_page'=>$limit,'no_found_rows'=>true];
 if($type==='track')$query['orderby']=\RadmanCT\Music\NEWEST_FIRST;
 return get_posts($query);
}
const FEATURED_META='_rdc_home_feature_tracks';
const FEATURED_LIMIT=4;

function selected_tracks(int $page):array {
 $saved=get_post_meta($page,FEATURED_META,true);
 if(!is_array($saved))return [];
 $ids=[];
 foreach($saved as $id){
  if(!is_scalar($id))continue;
  $id=absint($id);
  if($id&&!in_array($id,$ids,true))$ids[]=$id;
  if(count($ids)===FEATURED_LIMIT)break;
 }
 return $ids;
}

function featured():array {
 $query=['post_type'=>'track','post_status'=>'publish','has_password'=>false,'no_found_rows'=>true];
 $ids=selected_tracks((int)get_option('page_on_front'));
 if($ids){
  $tracks=get_posts($query+['post__in'=>$ids,'posts_per_page'=>FEATURED_LIMIT,'orderby'=>'post__in']);
  if($tracks)return $tracks;
 }
 return get_posts($query+['posts_per_page'=>3,'orderby'=>\RadmanCT\Music\NEWEST_FIRST]);
}
function image(int $id,string $size='medium_large',array $attributes=array()):string {return \RadmanCT\PageTitle\attachment_allowed($id)?wp_get_attachment_image($id,$size,false,$attributes):'';}
