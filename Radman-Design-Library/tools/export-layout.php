<?php
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
$out=['map'=>[],'media'=>[]];
foreach(['logo','logo_footer','footer_background'] as $key){
    $id=RadmanCT\media_id($key);if(!$id||!RadmanCT\PageTitle\attachment_allowed($id))continue;
    $file=get_post_meta($id,'_wp_attached_file',true);$meta=wp_get_attachment_metadata($id);$files=[$file];
    foreach(($meta['sizes']??[]) as $size)$files[]=dirname($file).'/'.$size['file'];
    $out['map'][$key]=$id;
    $out['media'][]=['id'=>$id,'file'=>$file,'files'=>$files,'meta'=>is_array($meta)?$meta:[],'title'=>get_the_title($id),'mime'=>get_post_mime_type($id)];
}
echo wp_json_encode($out,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
