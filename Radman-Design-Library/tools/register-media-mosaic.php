<?php
if(PHP_SAPI!=='cli')exit(1);require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||get_stylesheet()!=='radman-library')throw new RuntimeException('Wrong destination');
require_once ABSPATH.'wp-admin/includes/image.php';
require_once ABSPATH.'wp-admin/includes/media.php';
$map=array();foreach(array('image-1'=>'image-1.webp','image-2'=>'image-2.webp','image-3'=>'image-3.webp','poster'=>'poster.webp','video'=>'sample.mp4') as $key=>$name){
 $source='media-mosaic/'.$name;
 $existing=get_posts(array('post_type'=>'attachment','post_status'=>'inherit','posts_per_page'=>1,'fields'=>'ids','meta_key'=>'_rdl_mosaic_source','meta_value'=>$source));
 if($existing)$id=(int)$existing[0];else{
  $file=wp_upload_bits('mosaic-'.$name,null,file_get_contents('/opt/library/fixtures/media-mosaic/'.$name));if($file['error'])throw new RuntimeException($file['error']);
  $id=wp_insert_attachment(array('post_title'=>'رسانهٔ نمونهٔ چیدمان شطرنجی '.$key,'post_mime_type'=>$key==='video'?'video/mp4':'image/webp','post_status'=>'inherit'),$file['file'],0,true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
  wp_update_attachment_metadata($id,wp_generate_attachment_metadata($id,$file['file']));update_post_meta($id,'_rdl_mosaic_source',$source);update_post_meta($id,'_rdl_provenance','Selected existing Webtalik demonstration media; independent local copy, original source/license retained. Layout reference: https://museum.elementra.themerex.net/');
  if($key!=='video')update_post_meta($id,'_wp_attachment_image_alt',$key==='poster'?'پیش‌نمایش نمونهٔ ویدئویی':'طرح گرافیکی نمونه برای چیدمان شطرنجی');
 }$map[$key]=$id;
}
update_option('rdl_mosaic_media',$map,false);
$slug='media-mosaic';$module=RadmanLibrary\modules()[$slug];$post=get_page_by_path($slug,OBJECT,'rdl_design');
$id=$post?$post->ID:wp_insert_post(array('post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$module['title'],'post_excerpt'=>$module['motion']),true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
update_post_meta($id,'_rdl_module',$slug);if(!term_exists('gallery','rdl_group'))wp_insert_term('گالری','rdl_group',array('slug'=>'gallery'));
wp_set_object_terms($id,'gallery','rdl_group');wp_set_object_terms($id,'section','rdl_kind');wp_set_object_terms($id,array('native'),'rdl_fx');
echo get_permalink($id).PHP_EOL;echo wp_json_encode($map).PHP_EOL;
