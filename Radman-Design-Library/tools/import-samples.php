<?php
// Explicit local import only. Re-running preserves existing editorial changes.
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
if('http://127.0.0.1:8097'!==home_url()||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination; no import.');
$fixture=json_decode(file_get_contents('/opt/library/fixtures/webtalik.json'),true,512,JSON_THROW_ON_ERROR);
$uploads=wp_upload_dir();$ids=[];
foreach($fixture['media'] as $item){
    foreach($item['files'] as $file){
        if(str_contains($file,'..')||str_starts_with($file,'/')||!is_file($uploads['basedir'].'/'.$file))throw new RuntimeException('Selected local media missing.');
    }
    $existing=get_posts(['post_type'=>'attachment','post_status'=>'inherit','numberposts'=>1,'meta_key'=>'_rdl_webtalik_id','meta_value'=>$item['id'],'fields'=>'ids']);
    if($existing){$ids[$item['id']]=(int)$existing[0];continue;}
    $id=wp_insert_attachment(['post_title'=>$item['title'],'post_mime_type'=>$item['mime'],'post_status'=>'inherit'],$uploads['basedir'].'/'.$item['file'],0,true);
    if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
    update_attached_file($id,$uploads['basedir'].'/'.$item['file']);
    wp_update_attachment_metadata($id,$item['meta']);
    update_post_meta($id,'_wp_attachment_image_alt',$item['alt']);
    update_post_meta($id,'_rdl_webtalik_id',$item['id']);
    $ids[$item['id']]=$id;
}
foreach($fixture['map'] as &$id)$id=$ids[$id]??0;unset($id);
foreach($fixture['projects'] as &$item)$item['image']=$ids[$item['image']]??0;unset($item);
foreach($fixture['services'] as &$item){$item['image']=$ids[$item['image']]??0;unset($item['url']);}unset($item);
unset($fixture['media'],$fixture['home']['button_url']);
// Do not overwrite a fixture that has already been customized in this workshop.
if(!get_option('rdl_sample_data'))update_option('rdl_sample_data',$fixture,false);
$registry=json_decode(file_get_contents(get_template_directory().'/inc/samples.json'),true,512,JSON_THROW_ON_ERROR);
foreach($registry as $slug=>$module){
    if(get_page_by_path($slug,OBJECT,'rdl_design'))continue;
    $id=wp_insert_post(['post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$module['title'],'post_excerpt'=>$module['motion']],true);
    if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
    update_post_meta($id,'_rdl_module',$slug);
    wp_set_object_terms($id,$module['group'],'rdl_group');
    $image=$module['image']==='projects'?($fixture['projects'][0]['image']??0):($fixture['map'][$module['image']]??0);
    if($image)set_post_thumbnail($id,$image);
}
$starter=get_page_by_path('starter',OBJECT,'rdl_design');
if($starter&&$starter->post_title==='شروع یک طراحی مستقل'&&get_post_meta($starter->ID,'_rdl_module',true)==='starter')wp_update_post(['ID'=>$starter->ID,'post_status'=>'draft']);
echo count($registry)." designs available; ".count($ids)." selected attachments mapped to independent IDs.\n";
