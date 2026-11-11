<?php
if(PHP_SAPI!=='cli')exit(1);require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination.');
$slug='robot';$module=RadmanLibrary\modules()[$slug];
foreach(['interactive-3d'=>'سه‌بعدی تعاملی'] as $key=>$name)if(!term_exists($key,'rdl_group'))wp_insert_term($name,'rdl_group',['slug'=>$key]);
foreach(['three'=>'Three.js','pointer'=>'دنبال‌کردن نشانگر'] as $key=>$name)if(!term_exists($key,'rdl_fx'))wp_insert_term($name,'rdl_fx',['slug'=>$key]);
$post=get_page_by_path($slug,OBJECT,'rdl_design');
$id=wp_insert_post(['ID'=>$post?$post->ID:0,'post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$module['title'],'post_excerpt'=>$module['motion']],true);
if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
update_post_meta($id,'_rdl_module',$slug);wp_set_object_terms($id,$module['group'],'rdl_group');wp_set_object_terms($id,'section','rdl_kind');wp_set_object_terms($id,$module['effects'],'rdl_fx');
echo get_permalink($id)."\n";
