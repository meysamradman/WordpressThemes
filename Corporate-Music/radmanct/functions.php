<?php
namespace RadmanCT;
defined('ABSPATH')||exit;
foreach(array('setup','music','assets','site','page-title','brand','icons','ui/button','ui/contact','navigation','footer','services','seo','content/projects','content/media','preloader','client')as$module)require_once __DIR__.'/inc/'.$module.'.php';
add_action('wp',static function():void{
 if(is_front_page()){require_once __DIR__.'/inc/pages/music-home.php';require_once __DIR__.'/inc/media.php';return;}
 $blog=is_home()||is_category()||is_tag()||is_date()||is_author()||is_search()||is_singular('post');
 $portfolio=is_post_type_archive('project')||is_tax('project_category')||is_singular('project');
 if($blog||$portfolio)require_once __DIR__.'/inc/media.php';
 if($blog)require_once __DIR__.'/inc/blog.php';
 if(is_singular(array('post','project')))require_once __DIR__.'/inc/content/continuation.php';
});
