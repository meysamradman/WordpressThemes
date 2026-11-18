<?php
namespace RadmanCT\MusicHome;
defined('ABSPATH')||exit;
function data():array {static $data;return $data??=apply_filters('radmanct/music-home/data',array('label'=>'جدیدترین آثار','title'=>get_bloginfo('name'),'desc'=>get_bloginfo('description'),'tracks'=>'آخرین موزیک‌ها'));}
function entries(string $type,int $limit):array {return get_posts(array('post_type'=>$type==='track'?'track':'post','post_status'=>'publish','has_password'=>false,'posts_per_page'=>$limit,'no_found_rows'=>true));}
function featured():array {
 $selected=get_post((int)get_post_meta(get_queried_object_id(),'_rdc_home_feature_track',true));
 $latest=entries('track',3);
 if(!$selected||$selected->post_type!=='track'||$selected->post_status!=='publish'||$selected->post_password!=='')return $latest;
 $others=array_filter($latest,static fn($track)=>$track->ID!==$selected->ID);
 return array_slice(array_merge([$selected],$others),0,3);
}
function image(int $id,string $size='medium_large',array $attributes=array()):string {return \RadmanCT\PageTitle\attachment_allowed($id)?wp_get_attachment_image($id,$size,false,$attributes):'';}
