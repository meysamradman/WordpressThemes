<?php
namespace RadmanCT\Admin\Collections;
defined('ABSPATH')||exit;
foreach(['album','clip'] as $type){
 add_filter('manage_'.$type.'_posts_columns',static function(array $columns)use($type):array{
  $result=[];foreach($columns as $key=>$label){$result[$key]=$label;if($key==='cb')$result['rdc_cover']='کاور';if($key==='title')$result['rdc_media']=$type==='album'?'تعداد عکس':'فایل ویدئو';}return $result;
 });
 add_action('manage_'.$type.'_posts_custom_column',static function(string $column,int $id)use($type):void{
  if($column==='rdc_cover'){
   $cover=(int)get_post_thumbnail_id($id);
   if(!$cover&&$type==='album'){$meta=get_post_meta($id,'_rdc_album',true);$ids=\RadmanCT\Content\Media\gallery_ids(is_array($meta)?($meta['images']??[]):[]);$cover=$ids[0]??0;}
   if($cover)echo wp_get_attachment_image($cover,'thumbnail',false,['class'=>'rdc-adm-cover','loading'=>'lazy','alt'=>'']);else echo '<span aria-label="بدون کاور">—</span>';
  }elseif($column==='rdc_media'){
   $meta=get_post_meta($id,$type==='album'?'_rdc_album':'_rdc_clip',true);$meta=is_array($meta)?$meta:[];
   if($type==='album')echo esc_html(number_format_i18n(count(\RadmanCT\Content\Media\gallery_ids($meta['images']??[]))).' عکس');
   else{$file=\RadmanCT\Content\Media\video_id($meta['video']??0);if(!$file){echo 'انتخاب نشده';return;}$name=get_the_title($file);if(current_user_can('edit_post',$file))echo '<a href="'.esc_url(get_edit_post_link($file)).'">'.esc_html($name).'</a>';else echo esc_html($name);}
  }
 },10,2);
}
add_action('restrict_manage_posts',static function(string $type):void{
 if(!in_array($type,['album','clip'],true))return;$taxonomy=$type.'_category';$value=$_GET[$taxonomy]??'';$selected=is_string($value)?sanitize_title(wp_unslash($value)):'';
 echo '<label class="screen-reader-text" for="rdc-category-filter">فیلتر دسته‌بندی</label>';
 wp_dropdown_categories(['taxonomy'=>$taxonomy,'name'=>$taxonomy,'id'=>'rdc-category-filter','value_field'=>'slug','selected'=>$selected,'show_option_all'=>'همهٔ دسته‌ها','hide_empty'=>false,'hierarchical'=>true,'show_count'=>true]);
});
add_action('admin_enqueue_scripts',static function():void{
 $screen=get_current_screen();if($screen&&$screen->base==='edit'&&in_array($screen->post_type,['album','clip'],true))\RadmanCT\Assets\style('admin/gallery');
});
