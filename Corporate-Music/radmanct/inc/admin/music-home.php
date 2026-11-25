<?php
namespace RadmanCT\MusicHome;
defined('ABSPATH') || exit;

function is_home_editor(int $id):bool {
 return $id>0 && $id===(int)get_option('page_on_front');
}

const TRACK_PICKER_LIMIT=12;

function track_choices(string $search='',int $offset=0):array {
 $tracks=get_posts([
  'post_type'=>'track','post_status'=>'publish','has_password'=>false,
  'posts_per_page'=>TRACK_PICKER_LIMIT+1,'offset'=>$offset,
  'orderby'=>\RadmanCT\Music\NEWEST_FIRST,
  's'=>$search,'search_columns'=>['post_title'],
  'update_post_meta_cache'=>false,'update_post_term_cache'=>false,'no_found_rows'=>true,
 ]);
 $more=count($tracks)>TRACK_PICKER_LIMIT;
 $items=array_map(static fn(\WP_Post $track)=>['id'=>$track->ID,'title'=>wp_specialchars_decode(get_the_title($track),ENT_QUOTES)],array_slice($tracks,0,TRACK_PICKER_LIMIT));
 return ['items'=>$items,'more'=>$more];
}

add_action('admin_enqueue_scripts',static function(string $hook):void {
 $page=(int)get_the_ID();
 if($hook!=='post.php'||!is_home_editor($page)||!current_user_can('edit_post',$page))return;
 $asset=\RadmanCT\Assets\entry('src/js/admin/home-track-picker.js');
 if($asset)wp_enqueue_script('rdc-home-tracks',\RadmanCT\Assets\url($asset['file']),[],null,true);
});

add_action('wp_ajax_rdc_home_tracks',static function():void {
 $page=absint(is_string($_POST['page']??null)?$_POST['page']:0);
 if(!is_home_editor($page)||!current_user_can('edit_post',$page)||!check_ajax_referer('rdc_home_featured_'.$page,'nonce',false)){
  wp_send_json_error(null,403);
 }
 $search=is_string($_POST['search']??null)?sanitize_text_field(wp_unslash($_POST['search'])):'';
 $offset=is_string($_POST['offset']??null)?absint($_POST['offset']):0;
 if(strlen($search)>320||$offset>10000||$offset%TRACK_PICKER_LIMIT!==0)wp_send_json_error(null,400);
 wp_send_json_success(track_choices($search,$offset));
});


add_action('add_meta_boxes_page',static function(\WP_Post $post):void {
 if(!is_home_editor($post->ID))return;
 add_meta_box('rdc-home-featured','آهنگ‌ها و تصاویر اسلایدر صفحهٔ اول',static function(\WP_Post $page):void {
  wp_nonce_field('rdc_home_featured_'.$page->ID,'rdc_home_featured_nonce');
  $selected=selected_tracks($page->ID);
  $shown=featured();
  $banners=banners($page->ID);
  $choices=track_choices();
  $titles=array_column($choices['items'],'title','id');
  $shown_titles=[];
  foreach($shown as $song)$shown_titles[$song->ID]=get_the_title($song);
  ?>
  <p>تا سه آهنگ انتخاب کنید. اگر انتخاب دستی نداشته باشید، سه آهنگ تازه نمایش داده می‌شوند. برای تبلت و موبایل، اگر تصویر جدا انتخاب نکنید، تصویر دسکتاپ همان اسلاید نمایش داده می‌شود.</p>
  <?php for($index=0;$index<FEATURED_LIMIT;$index++):
   $field='rdc-home-featured-'.$index;
   $track=$selected[$index]??(!$selected?($shown[$index]->ID??0):0);
  ?>
   <fieldset data-rdc-track-picker data-step="<?php echo esc_attr(TRACK_PICKER_LIMIT); ?>" data-more="<?php echo $choices['more']?'1':'0'; ?>" data-page="<?php echo esc_attr($page->ID); ?>">
    <legend><strong>اسلاید <?php echo esc_html(number_format_i18n($index+1)); ?></strong></legend>
    <p data-rdc-track-search hidden>
     <label for="<?php echo esc_attr($field.'-search'); ?>">جست‌وجوی نام آهنگ</label>
     <input type="search" id="<?php echo esc_attr($field.'-search'); ?>" class="regular-text" maxlength="80" autocomplete="off" aria-controls="<?php echo esc_attr($field); ?>">
    </p>
    <label class="screen-reader-text" for="<?php echo esc_attr($field); ?>">انتخاب آهنگ</label>
    <select class="widefat" id="<?php echo esc_attr($field); ?>" name="rdc_home_feature_tracks[]">
     <option value=""><?php echo esc_html(!$selected&&$track?'خودکار: '.($shown_titles[$track]??''):'بدون انتخاب'); ?></option>
     <?php $options=isset($shown_titles[$selected[$index]??0])?$titles+[$selected[$index]=>$shown_titles[$selected[$index]]]:$titles;
     foreach($options as $id=>$title): ?>
      <option value="<?php echo esc_attr((string)$id); ?>" <?php selected($selected[$index]??0,$id); ?>><?php echo esc_html($title); ?></option>
     <?php endforeach; ?>
    </select>
    <p data-rdc-track-nav hidden>
     <button type="button" class="button" data-rdc-track-prev>نتایج قبلی</button>
     <button type="button" class="button" data-rdc-track-next>نتایج بعدی</button>
     <span role="status"></span>
    </p>
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
