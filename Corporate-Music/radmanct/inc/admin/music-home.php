<?php
namespace RadmanCT\MusicHome;
defined('ABSPATH') || exit;

function is_home_editor(int $id):bool {
 return $id>0 && $id===(int)get_option('page_on_front');
}

add_action('add_meta_boxes_page',static function(\WP_Post $post):void {
 if(!is_home_editor($post->ID))return;
 add_meta_box('rdc-home-featured','آهنگ‌ها و تصاویر اسلایدر صفحهٔ اول',static function(\WP_Post $page):void {
  wp_nonce_field('rdc_home_featured_'.$page->ID,'rdc_home_featured_nonce');
  $selected=selected_tracks($page->ID);
  $shown=featured();
  $banners=banners($page->ID);
  $tracks=get_posts([
   'post_type'=>'track','post_status'=>'publish','has_password'=>false,
   'posts_per_page'=>-1,'orderby'=>['title'=>'ASC','ID'=>'ASC'],
   'update_post_meta_cache'=>false,'update_post_term_cache'=>false,'no_found_rows'=>true,
  ]);
  $titles=[];
  foreach($tracks as $track)$titles[$track->ID]=get_the_title($track);
  ?>
  <p>تا سه آهنگ انتخاب کنید. اگر انتخاب دستی نداشته باشید، سه آهنگ تازه نمایش داده می‌شوند. برای تبلت و موبایل، اگر تصویر جدا انتخاب نکنید، تصویر دسکتاپ همان اسلاید نمایش داده می‌شود.</p>
  <?php for($index=0;$index<FEATURED_LIMIT;$index++):
   $field='rdc-home-featured-'.$index;
   $track=$selected[$index]??(!$selected?($shown[$index]->ID??0):0);
  ?>
   <fieldset>
    <legend><strong>اسلاید <?php echo esc_html(number_format_i18n($index+1)); ?></strong></legend>
    <label class="screen-reader-text" for="<?php echo esc_attr($field); ?>">انتخاب آهنگ</label>
    <select class="widefat" id="<?php echo esc_attr($field); ?>" name="rdc_home_feature_tracks[]">
     <option value=""><?php echo esc_html(!$selected&&$track?'خودکار: '.($titles[$track]??''):'بدون انتخاب'); ?></option>
     <?php foreach($titles as $id=>$title): ?>
      <option value="<?php echo esc_attr((string)$id); ?>" <?php selected($selected[$index]??0,$id); ?>><?php echo esc_html($title); ?></option>
     <?php endforeach; ?>
    </select>
    <input type="hidden" name="rdc_home_feature_original[]" value="<?php echo esc_attr($track); ?>">
    <?php foreach(BANNER_DEVICES as $device=>$label): ?>
     <?php \RadmanCT\Content\Media\single_picker("rdc_home_feature_banners[{$index}][{$device}]",banner_id($banners[$track][$device]??0),'image',$label); ?>
    <?php endforeach; ?>
   </fieldset>
  <?php endfor; ?>
  <p class="description">تصویر شاخص آهنگ، جایگزین اسلایدهای بدون بنر است. با تغییر آهنگ، تصاویر را برای آهنگ تازه انتخاب کنید.</p>
  <?php
 },'page','normal','high');
});

add_action('save_post_page',static function(int $id):void {
 if(!is_home_editor($id)||wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('edit_post',$id))return;
 $nonce=$_POST['rdc_home_featured_nonce']??null;
 $input=$_POST['rdc_home_feature_tracks']??null;
 if(!is_string($nonce)||!wp_verify_nonce(wp_unslash($nonce),'rdc_home_featured_'.$id)||!is_array($input)||count($input)!==FEATURED_LIMIT)return;
 $ids=[];
 $slots=[];
 foreach(wp_unslash($input) as $index=>$value){
  if(!is_string($value)||!ctype_digit($value))continue;
  $track=get_post((int)$value);
  if(!$track||$track->post_type!=='track'||$track->post_status!=='publish'||$track->post_password!==''||in_array($track->ID,$ids,true))continue;
  $ids[]=$track->ID;
  $slots[$index]=$track->ID;
 }
 $original=$_POST['rdc_home_feature_original']??null;
 $images=$_POST['rdc_home_feature_banners']??null;
 $saved=banners($id);
 if(is_array($original)&&is_array($images)&&count($images)===FEATURED_LIMIT){
  $automatic=$ids?[]:entries('track',FEATURED_LIMIT);
  foreach($images as $index=>$devices){
   $previous=absint(is_scalar($original[$index]??null)?$original[$index]:0);
   $track=$slots[$index]??($automatic[$index]->ID??0);
   if(!$track||!is_array($devices)||count($devices)!==count(BANNER_DEVICES)||(!$ids&&$track!==$previous))continue;
   $updated=[];
   foreach(BANNER_DEVICES as $device=>$label){
    $image=banner_id($devices[$device]??null);
    if($image&&current_user_can('read_post',$image)&&($track===$previous||$image!==($saved[$previous][$device]??0)))$updated[$device]=$image;
   }
   if($updated)$saved[$track]=$updated;
   elseif($track===$previous)unset($saved[$track]);
  }
  update_post_meta($id,BANNERS_META,$saved);
 }
 update_post_meta($id,FEATURED_META,$ids);
});
