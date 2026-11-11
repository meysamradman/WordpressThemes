<?php
// Read-only export. Run via stdin in the SOURCE container, never as a web route.
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
require_once get_template_directory().'/inc/home.php';
$names=['hero','hero-clean','offering-web','offering-wordpress','offering-systems','offering-apps','orb-gold','orb-ivory'];
foreach([48,45,46,47,49] as $n)$names[]='services-strip-'.$n;
$map=[];foreach($names as $name)$map[$name]=RadmanCT\media_id($name);
$projects=RadmanCT\Home\selected_work();
$ids=array_unique(array_merge(array_values($map),array_column($projects,'image')));
$media=[];
foreach($ids as $id){
 if(!$id||!RadmanCT\PageTitle\attachment_allowed($id))continue;
 $meta=wp_get_attachment_metadata($id);
 if(!is_array($meta))continue;
 $relative=get_post_meta($id,'_wp_attached_file',true);
 $dir=dirname($relative);$files=[$relative];
 foreach(($meta['sizes']??[]) as $size)$files[]=$dir.'/'.$size['file'];
 if(!empty($meta['original_image']))$files[]=$dir.'/'.$meta['original_image'];
 $media[]=['id'=>$id,'file'=>$relative,'files'=>array_values(array_unique($files)),'meta'=>$meta,'title'=>get_the_title($id),'mime'=>get_post_mime_type($id),'alt'=>get_post_meta($id,'_wp_attachment_image_alt',true)];
}
echo wp_json_encode(['source'=>'Corporate-Webtalik/radmanct','map'=>$map,'media'=>$media,'home'=>RadmanCT\Home\data(),'offerings'=>RadmanCT\Home\offerings(),'projects'=>$projects,'services'=>RadmanCT\Services\items(),'faq'=>apply_filters('radmanct/services/faq',[])],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
