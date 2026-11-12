<?php
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination.');
require_once ABSPATH.'wp-admin/includes/file.php';
require_once ABSPATH.'wp-admin/includes/media.php';
require_once ABSPATH.'wp-admin/includes/image.php';
$sources=array(
 'https://d3f86pfw66amx.cloudfront.net/uncode/wp-content/uploads/2026/04/Smiling-Woman-in-Gym-uai-360x240.webp',
 'https://d3f86pfw66amx.cloudfront.net/uncode/wp-content/uploads/2026/02/getty-images-BISlD1av31M-unsplash-uai-360x240.webp',
 'https://d3f86pfw66amx.cloudfront.net/uncode/wp-content/uploads/2026/02/john-arano-h4i9G-de7Po-unsplash-uai-360x240.webp',
 'https://d3f86pfw66amx.cloudfront.net/uncode/wp-content/uploads/2026/04/Urban-Runners-in-Motion-uai-360x240.webp',
);
$map=get_option('rdl_inline_media',array());
foreach($sources as $index=>$source){
 if(!empty($map[$index])&&wp_attachment_is_image((int)$map[$index]))continue;
 $path='/tmp/inline-'.$index.'.webp';
 if(!is_file($path))throw new RuntimeException('Copy the four reference WebP files into /tmp/inline-0.webp through inline-3.webp first.');
 $id=media_handle_sideload(array('name'=>'radman-inline-'.($index+1).'.webp','tmp_name'=>$path),0,'Radman inline image '.($index+1));
 if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
 update_post_meta($id,'_rdl_reference_url',$source);$map[$index]=$id;
}
update_option('rdl_inline_media',$map,false);
$parent=get_term_by('slug','text','rdl_group');
if(!term_exists('text-inline','rdl_group'))wp_insert_term('متن و تصویر داخل خط','rdl_group',array('slug'=>'text-inline','parent'=>$parent?$parent->term_id:0));
$slug='text-images';$module=RadmanLibrary\modules()[$slug];$post=get_page_by_path($slug,OBJECT,'rdl_design');
$id=wp_insert_post(array('ID'=>$post?$post->ID:0,'post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$module['title'],'post_excerpt'=>$module['motion']),true);
if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
update_post_meta($id,'_rdl_module',$slug);wp_set_object_terms($id,array('text','text-inline'),'rdl_group');wp_set_object_terms($id,'section','rdl_kind');wp_set_object_terms($id,$module['effects'],'rdl_fx');
echo get_permalink($id).PHP_EOL;
