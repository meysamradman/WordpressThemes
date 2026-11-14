<?php
namespace RadmanCT\Content\Collections;
defined('ABSPATH')||exit;
function register():void {
 foreach(['album'=>['آلبوم‌های عکس','آلبوم عکس','gallery','dashicons-format-gallery'],'clip'=>['ویدئوها','ویدئو','videos','dashicons-video-alt3']] as $type=>[$plural,$single,$slug,$icon]){
  register_post_type($type,['labels'=>['name'=>$plural,'singular_name'=>$single,'add_new_item'=>'افزودن '.$single,'edit_item'=>'ویرایش '.$single,'featured_image'=>'کاور '.$single,'set_featured_image'=>'انتخاب کاور'],'public'=>true,'show_in_rest'=>true,'has_archive'=>$slug,'rewrite'=>['slug'=>$slug,'with_front'=>false],'menu_icon'=>$icon,'supports'=>['title','editor','thumbnail','excerpt','revisions','author']]);
  $name=$type==='album'?'آلبوم':'ویدئو';$tax_slug=$type==='album'?'gallery':'video';
  register_taxonomy($type.'_category',$type,['labels'=>['name'=>'دسته‌های '.$name,'singular_name'=>'دستهٔ '.$name,'add_new_item'=>'افزودن دستهٔ '.$name,'parent_item'=>'دستهٔ مادر'],'public'=>true,'show_in_rest'=>true,'hierarchical'=>true,'show_admin_column'=>true,'show_in_quick_edit'=>true,'rewrite'=>['slug'=>$tax_slug.'-category','with_front'=>false]]);
  register_taxonomy($type.'_tag',$type,['labels'=>['name'=>'برچسب‌های '.$name,'singular_name'=>'برچسب '.$name],'public'=>true,'show_in_rest'=>true,'hierarchical'=>false,'show_admin_column'=>true,'show_in_quick_edit'=>true,'rewrite'=>['slug'=>$tax_slug.'-tag','with_front'=>false]]);
  register_post_meta($type,$type==='album'?'_rdc_album':'_rdc_clip',['type'=>'object','single'=>true,'show_in_rest'=>false,'auth_callback'=>static fn($allowed,$key,$id)=>current_user_can('edit_post',(int)$id)]);
 }
}
add_action('init',__NAMESPACE__.'\\register');
add_action('add_meta_boxes',static function():void{
 add_meta_box('rdc-album-images','عکس‌های آلبوم',static function(\WP_Post $post):void{
  wp_nonce_field('rdc_album_save','rdc_album_nonce');
  $meta=get_post_meta($post->ID,'_rdc_album',true);
  \RadmanCT\Content\Media\gallery_picker(\RadmanCT\Content\Media\gallery_ids(is_array($meta)?($meta['images']??[]):[]),'rdc_album_images');
 },'album','normal','high');
 add_meta_box('rdc-clip-file','فایل ویدئو',static function(\WP_Post $post):void{
  wp_nonce_field('rdc_clip_save','rdc_clip_nonce');$meta=get_post_meta($post->ID,'_rdc_clip',true);$id=\RadmanCT\Content\Media\video_id(is_array($meta)?($meta['video']??0):0);
  echo '<div data-rdc-md><input type="hidden" name="rdc_clip_video" data-media-value value="'.(int)$id.'"><p data-media-label>'.esc_html($id?get_the_title($id):'فایلی انتخاب نشده').'</p><p><button type="button" class="button" data-media-choose>انتخاب یا بارگذاری ویدئو</button> <button type="button" class="button-link-delete" data-media-remove>پاک‌کردن انتخاب</button></p><p class="description">فایل MP4 یا WebM را از رسانه انتخاب کنید. تصویر شاخص، کاور ویدئو است؛ پخش خودکار انجام نمی‌شود.</p></div>';
 },'clip','normal','high');
});
add_action('save_post',static function(int $id):void{
 $type=get_post_type($id);if(!in_array($type,['album','clip'],true)||wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('edit_post',$id))return;
 $nonce=$_POST['rdc_'.$type.'_nonce']??null;if(!is_string($nonce)||!wp_verify_nonce(wp_unslash($nonce),'rdc_'.$type.'_save'))return;
 if($type==='album'){
  $raw=$_POST['rdc_album_images']??null;if(!is_string($raw))return;
  $ids=\RadmanCT\Content\Media\gallery_ids(wp_unslash($raw));
  $ids=array_values(array_filter($ids,static fn($media)=>current_user_can('read_post',$media)));
  update_post_meta($id,'_rdc_album',['images'=>$ids]);
 }else{
  $raw=$_POST['rdc_clip_video']??null;if(!is_scalar($raw))return;
  $video=\RadmanCT\Content\Media\video_id(wp_unslash((string)$raw));
  if($video&&(!current_user_can('read_post',$video)||!in_array(get_post_mime_type($video),['video/mp4','video/webm'],true)))return;
  update_post_meta($id,'_rdc_clip',['video'=>$video]);
 }
});
add_action('pre_get_posts',static function(\WP_Query $query):void{
 if(is_admin()||!$query->is_main_query()||$query->is_feed())return;
 if($query->is_post_type_archive('album')||$query->is_tax(['album_category','album_tag']))$query->set('posts_per_page',9);
 if($query->is_post_type_archive('clip')||$query->is_tax(['clip_category','clip_tag']))$query->set('posts_per_page',6);
});

if(is_admin())require_once dirname(__DIR__).'/admin/collections.php';
