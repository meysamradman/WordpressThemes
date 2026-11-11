<?php
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination.');
require_once ABSPATH.'wp-admin/includes/image.php';
$items=json_decode(file_get_contents('/opt/library/fixtures/wellness/media.json'),true,512,JSON_THROW_ON_ERROR);$map=[];
foreach($items as $item){
    $ids=get_posts(['post_type'=>'attachment','post_status'=>'inherit','posts_per_page'=>1,'fields'=>'ids','meta_key'=>'_rdl_reference_url','meta_value'=>$item['source']]);
    if($ids)$id=$ids[0];else{
        $file=wp_upload_bits('wellness-'.basename($item['file']),null,file_get_contents('/opt/library/fixtures/wellness/'.basename($item['file'])));
        if($file['error'])throw new RuntimeException($file['error']);
        $id=wp_insert_attachment(['post_title'=>$item['label'],'post_status'=>'inherit','post_mime_type'=>'image/webp'],$file['file'],0,true);
        if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
        wp_update_attachment_metadata($id,wp_generate_attachment_metadata($id,$file['file']));update_post_meta($id,'_rdl_reference_url',$item['source']);update_post_meta($id,'_wp_attachment_image_alt',$item['label']);
    }
    $map[$item['key']]=$id;
}
if(!get_option('rdl_wellness_media'))update_option('rdl_wellness_media',$map,false);
$post=get_page_by_path('wellness-scroll',OBJECT,'rdl_design');if($post&&!has_post_thumbnail($post))set_post_thumbnail($post,$map['connect']);
echo "Three local wellness images ready.\n";
