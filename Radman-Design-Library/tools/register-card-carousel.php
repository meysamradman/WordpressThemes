<?php
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination.');
require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';
if(!wp_attachment_is('video',(int)get_option('rdl_card_video',0))){
 $id=media_handle_sideload(array('name'=>'radman-card-preview.mp4','tmp_name'=>'/tmp/ai-card-video.mp4'),0,'Radman card video');
 if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
 update_post_meta($id,'_rdl_reference_url','https://essentials.pixfort.com/ai-agency/wp-content/uploads/sites/64/2026/03/bg-video-office-3.mp4');update_option('rdl_card_video',$id,false);
}
if(!term_exists('carousel','rdl_group'))wp_insert_term('کاروسل','rdl_group',array('slug'=>'carousel'));
$parent=get_term_by('slug','carousel','rdl_group');
if(!term_exists('carousel-cards','rdl_group'))wp_insert_term('کاروسل کارت‌ها','rdl_group',array('slug'=>'carousel-cards','parent'=>$parent?(int)$parent->term_id:0));
foreach(array('native'=>'اسکرول بومی','drag'=>'کشیدن کارت‌ها','video'=>'ویدئوی محلی') as $slug=>$label)if(!term_exists($slug,'rdl_fx'))wp_insert_term($label,'rdl_fx',array('slug'=>$slug));
$slug='card-carousel';$module=RadmanLibrary\modules()[$slug];$post=get_page_by_path($slug,OBJECT,'rdl_design');
$id=wp_insert_post(array('ID'=>$post?$post->ID:0,'post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$module['title'],'post_excerpt'=>$module['motion']),true);
if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
update_post_meta($id,'_rdl_module',$slug);wp_set_object_terms($id,array('carousel','carousel-cards'),'rdl_group');wp_set_object_terms($id,'section','rdl_kind');wp_set_object_terms($id,$module['effects'],'rdl_fx');
echo get_permalink($id).PHP_EOL;
