<?php
namespace RadmanCT;
defined('ABSPATH')||exit;
require_once __DIR__.'/inc/content/biography.php';
require_once __DIR__.'/inc/pages/music-home.php';
if(is_admin())require_once __DIR__.'/inc/admin/music-home.php';
foreach(array('setup','music','videos','albums','content/collections','assets','site','page-title','brand','icons','ui/button','ui/contact','navigation','footer','services','seo','content/media','client')as$module)require_once __DIR__.'/inc/'.$module.'.php';
add_action('wp',static function():void{
 if(is_front_page()||is_page(array('about-us','contact-us')))require_once __DIR__.'/inc/ui/text-entrance.php';
 if(is_front_page()){require_once __DIR__.'/inc/media.php';return;}
 $blog=is_home()||is_category()||is_tag()||is_date()||is_author()||is_search()||is_singular('post');
 if($blog)require_once __DIR__.'/inc/media.php';
 if($blog)require_once __DIR__.'/inc/blog.php';
 if(is_singular('post'))require_once __DIR__.'/inc/content/continuation.php';
});
