<?php
if(PHP_SAPI!=='cli')exit(1);require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination.');
$parent=get_term_by('slug','text','rdl_group');
if(!term_exists('text-3d','rdl_group'))wp_insert_term('ورود سه‌بعدی متن','rdl_group',['slug'=>'text-3d','parent'=>$parent?$parent->term_id:0]);
$slug='text-3d';$m=RadmanLibrary\modules()[$slug];$post=get_page_by_path($slug,OBJECT,'rdl_design');
$id=wp_insert_post(['ID'=>$post?$post->ID:0,'post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$m['title'],'post_excerpt'=>$m['motion']],true);
if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
update_post_meta($id,'_rdl_module',$slug);wp_set_object_terms($id,['text','text-3d'],'rdl_group');wp_set_object_terms($id,'section','rdl_kind');wp_set_object_terms($id,$m['effects'],'rdl_fx');
$sticky=get_page_by_path('sticky-cards',OBJECT,'rdl_design');if($sticky)wp_update_post(['ID'=>$sticky->ID,'post_excerpt'=>RadmanLibrary\modules()['sticky-cards']['motion']]);
echo get_permalink($id)."\n";
