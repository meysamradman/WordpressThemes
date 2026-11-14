<?php
namespace RadmanLibrary\Collections;
defined('ABSPATH')||exit;
add_action('add_meta_boxes',static function():void{
 foreach(['album','clip'] as $type)add_meta_box('rdl-'.$type,'رسانه‌های '.($type==='album'?'آلبوم':'ویدئو'),static function(\WP_Post $post)use($type):void{
  wp_nonce_field('rdl_'.$type.'_save','rdl_media_nonce');$data=get_post_meta($post->ID,'_rdl_'.$type,true);$data=is_array($data)?$data:[];
  $fields=$type==='album'?['images'=>'عکس‌های آلبوم به ترتیب انتخاب']:['video'=>'فایل پخش اصلی','download_1080'=>'دانلود 1080P','download_720'=>'دانلود 720P','download_480'=>'دانلود 480P'];
  foreach($fields as $key=>$label){$value=$type==='album'?implode(',',\RadmanCT\Content\Media\gallery_ids($data[$key]??[])):absint($data[$key]??0);echo '<div data-rdl-media data-type="'.($type==='album'?'image':'video').'" data-multiple="'.($type==='album'?'1':'0').'"><p><strong>'.esc_html($label).'</strong></p><input type="hidden" name="rdl_media['.$key.']" value="'.esc_attr($value).'"><p data-rdl-media-label>'.esc_html($value?:'فایلی انتخاب نشده').'</p><button type="button" class="button" data-rdl-media-pick>انتخاب از رسانه</button> <button type="button" class="button-link-delete" data-rdl-media-clear>پاک‌کردن</button></div>';}
  echo '<p class="description">عنوان، توضیحات، خلاصه و کاور را در فیلدهای بومی وردپرس وارد کنید. برای هر کیفیت ویدئو فایل واقعی همان کیفیت را انتخاب کنید.</p>';
 },$type,'normal','high');
});
add_action('save_post',static function(int $id):void{
 $type=get_post_type($id);if(!in_array($type,['album','clip'],true)||wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('edit_post',$id))return;
 $nonce=$_POST['rdl_media_nonce']??null;$input=$_POST['rdl_media']??null;if(!is_string($nonce)||!wp_verify_nonce(wp_unslash($nonce),'rdl_'.$type.'_save')||!is_array($input))return;
 $data=get_post_meta($id,'_rdl_'.$type,true);$data=is_array($data)?$data:[];
 if($type==='album'){$raw=$input['images']??'';if(!is_string($raw))return;$data['images']=array_values(array_filter(\RadmanCT\Content\Media\gallery_ids(wp_unslash($raw)),static fn($image)=>current_user_can('read_post',$image)));}
 else foreach(['video','download_1080','download_720','download_480'] as $key){$raw=$input[$key]??0;if(!is_scalar($raw))return;$value=\RadmanCT\Content\Media\video_id($raw);$data[$key]=$value&&current_user_can('read_post',$value)?$value:0;}
 update_post_meta($id,'_rdl_'.$type,$data);
});
add_action('admin_enqueue_scripts',static function():void{$screen=get_current_screen();if(!$screen||$screen->base!=='post'||!in_array($screen->post_type,['album','clip'],true))return;wp_enqueue_media();wp_enqueue_script('rdl-collection-editor',get_template_directory_uri().'/src/js/admin/collection-editor.js',['media-editor'],filemtime(get_template_directory().'/src/js/admin/collection-editor.js'),true);});
