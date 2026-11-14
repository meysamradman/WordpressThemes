<?php
namespace RadmanCT\MusicHome;
defined('ABSPATH')||exit;
function data():array {static $data;return $data??=apply_filters('radmanct/music-home/data',array('label'=>'جدیدترین آثار','title'=>get_bloginfo('name'),'desc'=>get_bloginfo('description'),'tracks'=>'آخرین موزیک‌ها','videos_title'=>'آخرین ویدئوها','videos'=>array(),'socials'=>array()));}
function entries(string $type,int $limit):array {return get_posts(array('post_type'=>$type==='track'?'track':'post','post_status'=>'publish','has_password'=>false,'posts_per_page'=>$limit,'no_found_rows'=>true));}
function featured():array {return entries('track',3);}
function image(int $id,string $size='medium_large',array $attributes=array()):string {return \RadmanCT\PageTitle\attachment_allowed($id)?wp_get_attachment_image($id,$size,false,$attributes):'';}
