<?php
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination.');
$count=0;
foreach(RadmanLibrary\modules() as $slug=>$module){
    if($slug==='starter')continue;
    $post=get_page_by_path($slug,OBJECT,'rdl_design');
    if(!$post||$post->post_title===$module['title'])continue;
    $result=wp_update_post(['ID'=>$post->ID,'post_title'=>$module['title']],true);
    if(is_wp_error($result))throw new RuntimeException($result->get_error_message());
    ++$count;
}
echo $count." WordPress design titles updated; URLs unchanged.\n";
