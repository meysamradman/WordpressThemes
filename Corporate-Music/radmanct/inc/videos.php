<?php
namespace RadmanCT\Videos;
defined('ABSPATH')||exit;
function file(int $id):array {
 if(!\RadmanCT\PageTitle\attachment_allowed($id)||!in_array(get_post_mime_type($id),['video/mp4','video/webm'],true))return [];
 $url=wp_get_attachment_url($id);$uploads=wp_upload_dir(null,false);if(!$url||!str_starts_with($url,trailingslashit($uploads['baseurl'])))return [];
 return ['id'=>$id,'url'=>$url,'type'=>get_post_mime_type($id)];
}
function item(\WP_Post $post):array {
 $allowed=!post_password_required($post);$meta=$allowed?get_post_meta($post->ID,'_rdc_clip',true):[];
 $id=is_array($meta)&&is_scalar($meta['video']??0)?absint($meta['video']??0):0;$video=$allowed?file($id):[];
 return ['title'=>get_the_title($post),'desc'=>$allowed?get_the_excerpt($post):'ویدئوی محافظت‌شده','video'=>$video['id']??0,'image'=>$allowed?(int)get_post_thumbnail_id($post):0,'url'=>get_permalink($post)];
}
function data():array {
 $posts=get_posts(['post_type'=>'clip','post_status'=>'publish','has_password'=>false,'posts_per_page'=>3,'no_found_rows'=>true]);
 return apply_filters('radmanct/videos/data',['title'=>'آخرین ویدئوها','items'=>array_map(__NAMESPACE__.'\\item',$posts)]);
}
