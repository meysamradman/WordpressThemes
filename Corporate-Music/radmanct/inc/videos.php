<?php
namespace RadmanCT\Videos;
defined('ABSPATH')||exit;
function aparat_id($value):string {
 if(!is_string($value))return '';
 $value=trim($value);
 if(preg_match('/^[a-zA-Z0-9]{5,32}$/D',$value))return $value;
 $parts=wp_parse_url($value);
 if(!is_array($parts)||($parts['scheme']??'')!=='https'||!in_array(strtolower($parts['host']??''),['aparat.com','www.aparat.com'],true)||isset($parts['user'])||isset($parts['port']))return '';
 return preg_match('~^/v/([a-zA-Z0-9]{5,32})/?$~D',$parts['path']??'',$match)?$match[1]:'';
}
function embed_url(mixed $value):string {
 $hash=aparat_id($value);
 return $hash?'https://www.aparat.com/video/video/embed/videohash/'.$hash.'/vt/frame':'';
}
function aparat_download_button(mixed $value,string $class):string {
 $hash=aparat_id($value);
 return $hash?\RadmanCT\button('دانلود از آپارات','https://www.aparat.com/v/'.$hash,[
  'class'=>$class,'variant'=>'download','new_tab'=>true,
 ]):'';
}
function file(int $id):array {
 if(!\RadmanCT\PageTitle\attachment_allowed($id)||!in_array(get_post_mime_type($id),['video/mp4','video/webm'],true))return [];
 $url=wp_get_attachment_url($id);$uploads=wp_upload_dir(null,false);if(!$url||!str_starts_with($url,trailingslashit($uploads['baseurl'])))return [];
 return ['id'=>$id,'url'=>$url,'type'=>get_post_mime_type($id)];
}
function item(\WP_Post $post):array {
 $allowed=!post_password_required($post);$meta=$allowed?get_post_meta($post->ID,'_rdc_clip',true):[];
 $id=is_array($meta)&&is_scalar($meta['video']??0)?absint($meta['video']??0):0;$video=$allowed?file($id):[];
 return ['id'=>$post->ID,'title'=>get_the_title($post),'desc'=>$allowed?get_the_excerpt($post):'ویدئوی محافظت‌شده','aparat'=>$allowed&&is_array($meta)?aparat_id($meta['aparat']??''):'','video'=>$video['id']??0,'image'=>$allowed?(int)get_post_thumbnail_id($post):0,'url'=>get_permalink($post)];
}
function data(int $limit=12):array {
 $posts=get_posts(['post_type'=>'clip','post_status'=>'publish','has_password'=>false,'posts_per_page'=>$limit,'no_found_rows'=>true]);
 return apply_filters('radmanct/videos/data',['title'=>'آخرین ویدئوها','items'=>array_map(__NAMESPACE__.'\\item',$posts)]);
}

function downloads(int $id):array {
 if(post_password_required($id))return [];
 $meta=get_post_meta($id,'_rdc_clip',true);$meta=is_array($meta)?$meta:[];$out=[];
 foreach([1080,720,480] as $quality){$value=$meta['download_'.$quality]??0;$out[$quality]=is_scalar($value)?file(absint($value)):[];}
 return $out;
}
