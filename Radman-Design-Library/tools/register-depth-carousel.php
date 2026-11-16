<?php
if(PHP_SAPI!=='cli')exit(1);require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||get_stylesheet()!=='radman-library')throw new RuntimeException('Wrong destination');
if(!term_exists('carousel','rdl_group'))wp_insert_term('کاروسل','rdl_group',['slug'=>'carousel']);
$parent=get_term_by('slug','carousel','rdl_group');
if(!term_exists('carousel-depth','rdl_group'))wp_insert_term('کاروسل سه‌بعدی',['slug'=>'carousel-depth','parent'=>(int)$parent->term_id]);
foreach(['css-3d'=>'پرسپکتیو CSS','drag'=>'کشیدن کارت‌ها','smooth'=>'حرکت نرم'] as $slug=>$label)if(!term_exists($slug,'rdl_fx'))wp_insert_term($label,'rdl_fx',['slug'=>$slug]);
$slug='depth-carousel';$module=RadmanLibrary\modules()[$slug];$post=get_page_by_path($slug,OBJECT,'rdl_design');
$id=wp_insert_post(['ID'=>$post?$post->ID:0,'post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$module['title'],'post_excerpt'=>$module['motion']],true);
if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
update_post_meta($id,'_rdl_module',$slug);wp_set_object_terms($id,['carousel','carousel-depth'],'rdl_group');wp_set_object_terms($id,'section','rdl_kind');wp_set_object_terms($id,$module['effects'],'rdl_fx');
echo get_permalink($id).PHP_EOL;
