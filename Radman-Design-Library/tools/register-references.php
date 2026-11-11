<?php
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination.');
require_once ABSPATH.'wp-admin/includes/image.php';
$items=json_decode(file_get_contents('/opt/library/fixtures/azurio/media.json'),true,512,JSON_THROW_ON_ERROR);
$images=[];
foreach($items as $item){
    $existing=get_posts(['post_type'=>'attachment','post_status'=>'inherit','posts_per_page'=>1,'meta_key'=>'_rdl_reference_url','meta_value'=>$item['source'],'fields'=>'ids']);
    if($existing)$id=(int)$existing[0];
    else{
        $file='/opt/library/fixtures/azurio/'.basename($item['file']);
        if(!is_file($file))throw new RuntimeException('Reference image missing.');
        $upload=wp_upload_bits('azurio-'.basename($item['file']),null,file_get_contents($file));
        if($upload['error'])throw new RuntimeException($upload['error']);
        $id=wp_insert_attachment(['post_title'=>$item['label'],'post_mime_type'=>'image/webp','post_status'=>'inherit'],$upload['file'],0,true);
        if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
        wp_update_attachment_metadata($id,wp_generate_attachment_metadata($id,$upload['file']));
        update_post_meta($id,'_wp_attachment_image_alt',$item['label']);
        update_post_meta($id,'_rdl_reference_url',$item['source']);
    }
    $images[]=['id'=>$id,'label'=>$item['label']];
}
if(!get_option('rdl_reference_media'))update_option('rdl_reference_media',$images,false);
if(!term_exists('process','rdl_group'))wp_insert_term('فرایند همکاری','rdl_group',['slug'=>'process']);
foreach(['process-steps','project-marquee'] as $slug){
    if(get_page_by_path($slug,OBJECT,'rdl_design'))continue;
    $module=RadmanLibrary\modules()[$slug];
    $id=wp_insert_post(['post_type'=>'rdl_design','post_status'=>'publish','post_title'=>$module['title'],'post_name'=>$slug,'post_excerpt'=>$module['motion']],true);
    if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
    update_post_meta($id,'_rdl_module',$slug);wp_set_object_terms($id,$module['group'],'rdl_group');
    if($slug==='project-marquee')set_post_thumbnail($id,$images[3]['id']);
}
echo 'Two reference designs registered; '.count($images)." local media attachments ready.\n";
