<?php
namespace RadmanCT\MusicHome;
defined('ABSPATH')||exit;
function data():array {return apply_filters('radmanct/music-home/data',array('label'=>'دنیای صدا و روایت','title'=>get_bloginfo('name'),'desc'=>get_bloginfo('description'),'tracks'=>'تازه‌ترین آهنگ‌ها','posts'=>'تازه‌های وبلاگ'));}
function entries(string $type,int $limit):array {return get_posts(array('post_type'=>$type==='track'?'track':'post','post_status'=>'publish','has_password'=>false,'posts_per_page'=>$limit,'no_found_rows'=>true));}
