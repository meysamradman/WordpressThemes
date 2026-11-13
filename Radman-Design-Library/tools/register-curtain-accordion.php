<?php
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready')||get_stylesheet()!=='radman-library')throw new RuntimeException('Wrong destination.');
require_once ABSPATH.'wp-admin/includes/image.php';
// Trusted reference SVGs are embedded as img; this filter exists only in this CLI process.
add_filter('upload_mimes',static function(array $m):array{$m['svg']='image/svg+xml';return $m;});
$base='/opt/library/fixtures/curtain-accordion/';$map=array();
foreach(json_decode(file_get_contents($base.'media.json'),true,512,JSON_THROW_ON_ERROR) as $item){
 $ids=get_posts(array('post_type'=>'attachment','post_status'=>'inherit','posts_per_page'=>1,'fields'=>'ids','meta_key'=>'_rdl_reference_url','meta_value'=>$item['source']));
 if($ids)$id=$ids[0];else{
  $file=wp_upload_bits('curtain-'.basename($item['file']),null,file_get_contents($base.basename($item['file'])));if($file['error'])throw new RuntimeException($file['error']);
  $svg=str_ends_with($item['file'],'.svg');
  $id=wp_insert_attachment(array('post_title'=>$item['label'],'post_status'=>'inherit','post_mime_type'=>$svg?'image/svg+xml':'image/webp'),$file['file'],0,true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
  if($svg)wp_update_attachment_metadata($id,array('width'=>32,'height'=>32,'file'=>_wp_relative_upload_path($file['file'])));else wp_update_attachment_metadata($id,wp_generate_attachment_metadata($id,$file['file']));
  update_post_meta($id,'_rdl_reference_url',$item['source']);update_post_meta($id,'_wp_attachment_image_alt',$item['label']);
 }$map[$item['key']]=$id;
}
update_option('rdl_curtain_media',$map,false);
$parent=get_term_by('slug','accordion','rdl_group');
if(!term_exists('accordion-curtain','rdl_group')){$term=wp_insert_term('آکاردئون ستون‌های بازشونده','rdl_group',array('slug'=>'accordion-curtain','parent'=>$parent?(int)$parent->term_id:0));if(is_wp_error($term))throw new RuntimeException($term->get_error_message());}
$slug='curtain-accordion';$module=RadmanLibrary\modules()[$slug];$post=get_page_by_path($slug,OBJECT,'rdl_design');
$id=wp_insert_post(array('ID'=>$post?$post->ID:0,'post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$module['title'],'post_excerpt'=>$module['motion']),true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
update_post_meta($id,'_rdl_module',$slug);
foreach(array('rdl_group'=>array('accordion','accordion-curtain'),'rdl_kind'=>array('section'),'rdl_fx'=>$module['effects']) as $taxonomy=>$terms){$result=wp_set_object_terms($id,$terms,$taxonomy);if(is_wp_error($result))throw new RuntimeException($result->get_error_message());}
echo get_permalink($id).PHP_EOL;
