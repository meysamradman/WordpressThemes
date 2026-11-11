<?php
if(PHP_SAPI!=='cli')exit(1);require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination.');
require_once ABSPATH.'wp-admin/includes/image.php';
$items=json_decode(file_get_contents('/opt/library/fixtures/elementra/media.json'),true,512,JSON_THROW_ON_ERROR);$map=[];
foreach($items as $item){
 $ids=get_posts(['post_type'=>'attachment','post_status'=>'inherit','posts_per_page'=>1,'fields'=>'ids','meta_key'=>'_rdl_reference_url','meta_value'=>$item['source']]);
 if($ids)$id=$ids[0];else{
  $file=wp_upload_bits('elementra-'.basename($item['file']),null,file_get_contents('/opt/library/fixtures/elementra/'.basename($item['file'])));if($file['error'])throw new RuntimeException($file['error']);
  $id=wp_insert_attachment(['post_title'=>$item['label'],'post_mime_type'=>'image/jpeg','post_status'=>'inherit'],$file['file'],0,true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
  wp_update_attachment_metadata($id,wp_generate_attachment_metadata($id,$file['file']));update_post_meta($id,'_rdl_reference_url',$item['source']);update_post_meta($id,'_wp_attachment_image_alt',$item['label']);
 }$map[$item['key']]=$id;
}
if(!get_option('rdl_elementra_media'))update_option('rdl_elementra_media',$map,false);
$parent=get_term_by('slug','accordion','rdl_group');
foreach(['image-accordion'=>['آکاردئون تصویری','accordion-image'],'card-accordion'=>['آکاردئون تصویر و متن','accordion-card']] as $slug=>[$label,$sub]){
 if(!term_exists($sub,'rdl_group'))wp_insert_term($label,'rdl_group',['slug'=>$sub,'parent'=>$parent?$parent->term_id:0]);
 $post=get_page_by_path($slug,OBJECT,'rdl_design');$module=RadmanLibrary\modules()[$slug];
 if(!$post){$id=wp_insert_post(['post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$module['title'],'post_excerpt'=>$module['motion']],true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());update_post_meta($id,'_rdl_module',$slug);wp_set_object_terms($id,['accordion',$sub],'rdl_group');wp_set_object_terms($id,'section','rdl_kind');wp_set_object_terms($id,$module['effects'],'rdl_fx');}else $id=$post->ID;
 if(!has_post_thumbnail($id))set_post_thumbnail($id,$map['street']);
}
echo "Two accordion designs and three local reference attachments ready.\n";
