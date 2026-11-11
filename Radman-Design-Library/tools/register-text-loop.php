<?php
if(PHP_SAPI!=='cli')exit(1);require '/var/www/html/wp-load.php';if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination.');
$slug='text-loop';$m=RadmanLibrary\modules()[$slug];if(!get_page_by_path($slug,OBJECT,'rdl_design')){$id=wp_insert_post(['post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$m['title'],'post_excerpt'=>$m['motion']],true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());update_post_meta($id,'_rdl_module',$slug);wp_set_object_terms($id,['text','text-loop'],'rdl_group');wp_set_object_terms($id,'section','rdl_kind');wp_set_object_terms($id,$m['effects'],'rdl_fx');}
foreach(['text-marquee','sticky-cards'] as $slug){$post=get_page_by_path($slug,OBJECT,'rdl_design');if($post)wp_update_post(['ID'=>$post->ID,'post_excerpt'=>RadmanLibrary\modules()[$slug]['motion']]);}
echo "Standalone shared text loop registered.\n";
