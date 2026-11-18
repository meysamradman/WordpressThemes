<?php
namespace RadmanCT\MusicHome;
defined('ABSPATH') || exit;

function is_home_editor(int $id):bool {
 return $id>0 && $id===(int)get_option('page_on_front');
}

add_action('add_meta_boxes_page',static function(\WP_Post $post):void {
 if(!is_home_editor($post->ID))return;
 add_meta_box('rdc-home-featured','آهنگ‌های اسلایدر بالای صفحه',static function(\WP_Post $page):void {
  wp_nonce_field('rdc_home_featured_'.$page->ID,'rdc_home_featured_nonce');
  $selected=selected_tracks($page->ID);
  $tracks=get_posts([
   'post_type'=>'track','post_status'=>'publish','has_password'=>false,
   'posts_per_page'=>-1,'orderby'=>['title'=>'ASC','ID'=>'ASC'],
   'update_post_meta_cache'=>false,'update_post_term_cache'=>false,'no_found_rows'=>true,
  ]);
  ?>
  <p>حداکثر چهار آهنگ انتخاب کنید. جایگاه ۱ اولین اسلاید است. اگر همهٔ جایگاه‌ها خالی باشند، تا سه آهنگِ آخر بر اساس تاریخ انتشار نمایش داده می‌شوند.</p>
  <?php for($index=0;$index<FEATURED_LIMIT;$index++):$field='rdc-home-featured-'.$index; ?>
   <p>
    <label for="<?php echo esc_attr($field); ?>">جایگاه <?php echo esc_html(number_format_i18n($index+1)); ?></label>
    <select class="widefat" id="<?php echo esc_attr($field); ?>" name="rdc_home_feature_tracks[]">
     <option value="">بدون انتخاب</option>
     <?php foreach($tracks as $track): ?>
      <option value="<?php echo esc_attr((string)$track->ID); ?>" <?php selected($selected[$index]??0,$track->ID); ?>><?php echo esc_html(get_the_title($track)); ?></option>
     <?php endforeach; ?>
    </select>
   </p>
  <?php endfor;
 },'page','normal','high');
});

add_action('save_post_page',static function(int $id):void {
 if(!is_home_editor($id)||wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('edit_post',$id))return;
 $nonce=$_POST['rdc_home_featured_nonce']??null;
 $input=$_POST['rdc_home_feature_tracks']??null;
 if(!is_string($nonce)||!wp_verify_nonce(wp_unslash($nonce),'rdc_home_featured_'.$id)||!is_array($input)||count($input)!==FEATURED_LIMIT)return;
 $ids=[];
 foreach(wp_unslash($input) as $value){
  if(!is_string($value)||!ctype_digit($value))continue;
  $track=get_post((int)$value);
  if(!$track||$track->post_type!=='track'||$track->post_status!=='publish'||$track->post_password!==''||in_array($track->ID,$ids,true))continue;
  $ids[]=$track->ID;
 }
 update_post_meta($id,FEATURED_META,$ids);
});
