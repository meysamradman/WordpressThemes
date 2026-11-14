<?php
if(PHP_SAPI!=='cli')exit(1);require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||get_stylesheet()!=='radman-library')throw new RuntimeException('Wrong destination');
foreach(['gallery'=>['گالری',['gallery-grid'=>'آرشیو آلبوم‌ها','gallery-detail'=>'جزئیات آلبوم']],'video'=>['ویدئو',['video-grid'=>'آرشیو ویدئو','video-detail'=>'جزئیات ویدئو']]] as $family=>[$name,$children]){
 $parent=get_term_by('slug',$family,'rdl_group');if(!$parent){$created=wp_insert_term($name,'rdl_group',['slug'=>$family]);if(is_wp_error($created))throw new RuntimeException($created->get_error_message());$parent=get_term($created['term_id'],'rdl_group');}
 foreach($children as $slug=>$label)if(!term_exists($slug,'rdl_group'))wp_insert_term($label,'rdl_group',['slug'=>$slug,'parent'=>$parent->term_id]);
}
foreach(['gallery-archive','gallery-single','video-archive','video-single'] as $slug){
 $module=RadmanLibrary\modules()[$slug];$post=get_page_by_path($slug,OBJECT,'rdl_design');$id=$post?$post->ID:wp_insert_post(['post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$module['title'],'post_excerpt'=>$module['motion']],true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
 update_post_meta($id,'_rdl_module',$slug);wp_set_object_terms($id,[$module['group'],$module['subgroup']],'rdl_group');wp_set_object_terms($id,'page','rdl_kind');wp_set_object_terms($id,['native'],'rdl_fx');echo get_permalink($id).PHP_EOL;
}
$images=get_posts(['post_type'=>'attachment','post_mime_type'=>'image','post_status'=>'inherit','posts_per_page'=>100,'fields'=>'ids']);$images=array_values(array_filter($images,static function($id){$meta=wp_get_attachment_metadata($id);return RadmanCT\PageTitle\attachment_allowed($id)&&in_array(get_post_mime_type($id),['image/jpeg','image/png','image/webp'],true)&&(int)($meta['width']??0)>=400&&(int)($meta['height']??0)>=300;}));
if(count($images)<2)throw new RuntimeException('Local demo images missing');
$media=get_option('rdl_mosaic_media',[]);$video=(int)($media['video']??0);if(!$video){$ids=get_posts(['post_type'=>'attachment','post_mime_type'=>'video','post_status'=>'inherit','posts_per_page'=>1,'fields'=>'ids']);$video=(int)($ids[0]??0);}if(!RadmanLibrary\Collections\file($video))throw new RuntimeException('Local demo video missing');
foreach(['album'=>4,'clip'=>5] as $type=>$count){
 $term=term_exists('demo-'.$type,$type.'_category')?:wp_insert_term('نمونه‌های کتابخانه',$type.'_category',['slug'=>'demo-'.$type]);if(is_wp_error($term))throw new RuntimeException($term->get_error_message());
 for($i=1;$i<=$count;$i++){$slug='library-'.$type.'-'.$i;$post=get_page_by_path($slug,OBJECT,$type);if($post)continue;
  $id=wp_insert_post(['post_type'=>$type,'post_status'=>'publish','post_name'=>$slug,'post_title'=>($type==='album'?'آلبوم تصویری ':'روایت تصویری ').$i,'post_excerpt'=>'محتوای نمایشی مستقل کتابخانه؛ کاور و رسانه از وردپرس قابل جایگزینی است.','post_content'=>'<p>این نمونه برای نمایش چیدمان و امکانات قالب است. محتوای نهایی و رسانه‌ها در وردپرس مقصد جایگزین می‌شوند.</p>'],true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
  set_post_thumbnail($id,$images[($i-1)%count($images)]);wp_set_object_terms($id,[(int)$term['term_id']],$type.'_category');update_post_meta($id,'_rdl_demo_collection',1);
  if($type==='album'){update_post_meta($id,'_rdl_album',['images'=>array_slice($images,0,20)]);}else{$data=['video'=>$video];$meta=wp_get_attachment_metadata($video);$height=(int)($meta['height']??0);if(in_array($height,[1080,720,480],true))$data['download_'.$height]=$video;update_post_meta($id,'_rdl_clip',$data);}
 }
}
foreach(['album','clip'] as $type)foreach(get_posts(['post_type'=>$type,'posts_per_page'=>20,'meta_key'=>'_rdl_demo_collection','meta_value'=>1]) as $index=>$post){set_post_thumbnail($post->ID,$images[$index%count($images)]);if($type==='album')update_post_meta($post->ID,'_rdl_album',['images'=>array_slice($images,0,20)]);}
flush_rewrite_rules(false);echo 'Registered four designs and independent local demo content; existing music untouched.'.PHP_EOL;
