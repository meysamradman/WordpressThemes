<?php
namespace RadmanCT\Content\Media;
defined('ABSPATH')||exit;
function video_id($value):int {
 $id=filter_var($value,FILTER_VALIDATE_INT);return $id&&$id>0&&wp_attachment_is('video',$id)&&in_array(get_post_mime_type($id),array('video/mp4','video/webm','video/ogg'),true)?$id:0;
}
function gallery_ids($value):array {
 if(is_string($value))$value=explode(',',$value);
 if(!is_array($value))return array();
 return array_values(array_filter(array_unique(array_map('absint',array_slice($value,0,100))),static fn($id)=>wp_attachment_is_image($id)));
}
add_action('init',static function():void{
 foreach(array('post','project')as$type){
  register_post_meta($type,'_webtalik_card_video',array('type'=>'integer','single'=>true,'default'=>0,'sanitize_callback'=>__NAMESPACE__.'\\video_id','auth_callback'=>static fn($allowed,$key,$id)=>current_user_can('edit_post',(int)$id)));
  register_post_meta($type,'_webtalik_card_mode',array('type'=>'string','single'=>true,'default'=>'auto','sanitize_callback'=>static fn($value)=>$value==='hover'?'hover':'auto','auth_callback'=>static fn($allowed,$key,$id)=>current_user_can('edit_post',(int)$id)));
 }
},20);
add_action('add_meta_boxes',static function():void{
 foreach(array('post','project')as$type)add_meta_box('rdc-card-md',__('Card media','radmanct'),__NAMESPACE__.'\\fields',$type,'side');
 add_meta_box('rdc-prj-gal',__('Project gallery','radmanct'),__NAMESPACE__.'\\gallery_fields','project','normal','high');
});
function fields(\WP_Post $post):void {
 wp_nonce_field('rdc-card-md','rdct_media_nonce');$id=(int)get_post_meta($post->ID,'_webtalik_card_video',true);$mode=get_post_meta($post->ID,'_webtalik_card_mode',true);
 echo '<div data-rdc-md><p>'.esc_html__('Featured image remains the poster. Video preview is optional.','radmanct').'</p><input type="hidden" name="rdct_card_video" value="'.$id.'"><p data-media-label>'.esc_html($id?get_the_title($id):__('No video selected','radmanct')).'</p><p><button type="button" class="button" data-media-choose>'.esc_html__('Choose video','radmanct').'</button> <button type="button" class="button-link-delete" data-media-remove>'.esc_html__('Remove','radmanct').'</button></p><p><label for="rdc-card-mode">'.esc_html__('Preview behavior','radmanct').'</label></p><select id="rdc-card-mode" name="rdct_card_mode"><option value="auto" '.selected($mode!=='hover',true,false).'>'.esc_html__('Play when visible','radmanct').'</option><option value="hover" '.selected($mode,'hover',false).'>'.esc_html__('Play on hover','radmanct').'</option></select></div>';
}
function gallery_fields(\WP_Post $post):void {
 wp_nonce_field('rdc-prj-gal','rdct_gallery_nonce');
 $meta=get_post_meta($post->ID,'_webtalik_project',true);$gallery=gallery_ids(is_array($meta)?($meta['gallery']??array()):array());
 ?>
 <div class="rdc-adm-gal" data-rdc-gal>
  <input type="hidden" name="rdct_project_gallery" value="<?php echo esc_attr(implode(',',$gallery)); ?>">
  <p class="description"><?php esc_html_e('Add images and drag them into order in the gallery editor. Click a thumbnail to view the original image.','radmanct'); ?></p>
  <div class="rdc-adm-gal__bar">
   <button type="button" class="button button-secondary" data-gallery-choose><?php esc_html_e('Add / edit gallery','radmanct'); ?></button>
   <button type="button" class="button-link-delete" data-gallery-remove <?php disabled(!$gallery); ?>><?php esc_html_e('Clear gallery','radmanct'); ?></button>
   <span data-gallery-count role="status" aria-live="polite"><?php echo esc_html(sprintf(__('%d images selected','radmanct'),count($gallery))); ?></span>
  </div>
  <p class="rdc-adm-gal__emp" data-gallery-empty <?php if($gallery)echo 'hidden'; ?>><?php esc_html_e('No gallery images yet. Add images from the media library or upload new ones.','radmanct'); ?></p>
  <p data-gallery-error role="alert" hidden></p>
  <ol class="rdc-adm-gal__list" data-gallery-list>
   <?php foreach($gallery as $id): ?>
    <li class="rdc-adm-gal__itm" data-gallery-id="<?php echo esc_attr($id); ?>">
     <a class="rdc-adm-gal__pv" href="<?php echo esc_url(wp_get_attachment_url($id)); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr(sprintf(__('View original image: %s (opens in a new tab)','radmanct'),get_the_title($id))); ?>">
      <?php echo wp_get_attachment_image($id,'thumbnail',false,array('alt'=>'','loading'=>'lazy')); ?>
     </a>
     <span class="rdc-adm-gal__name"><?php echo esc_html(get_the_title($id)); ?></span>
     <div class="rdc-adm-gal__acts">
      <button type="button" class="button button-small" data-gallery-move="-1" aria-label="<?php esc_attr_e('Move image earlier','radmanct'); ?>"><span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span></button>
      <button type="button" class="button button-small" data-gallery-move="1" aria-label="<?php esc_attr_e('Move image later','radmanct'); ?>"><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button>
      <button type="button" class="button-link-delete" data-gallery-delete><?php esc_html_e('Remove','radmanct'); ?></button>
     </div>
    </li>
   <?php endforeach; ?>
  </ol>
  <p class="description"><?php esc_html_e('Removing an image here keeps it in the media library. Save the project to apply gallery changes.','radmanct'); ?></p>
 </div>
 <?php
}
add_action('admin_enqueue_scripts',static function():void{
 $screen=get_current_screen();if(!$screen||$screen->base!=='post'||!in_array($screen->post_type,array('post','project'),true))return;
 wp_enqueue_media();$asset=\RadmanCT\Assets\entry('src/js/admin/media-editor.js');if($asset)wp_enqueue_script('rdc-md-ed',\RadmanCT\Assets\url($asset['file']),array('media-editor'),null,true);
 if($screen->post_type==='project'){
  \RadmanCT\Assets\style('admin/gallery');
  wp_localize_script('rdc-md-ed','rdcGal',array('count'=>__('%d images selected','radmanct'),'view'=>__('View original image: %s (opens in a new tab)','radmanct'),'remove'=>__('Remove','radmanct'),'earlier'=>__('Move image earlier','radmanct'),'later'=>__('Move image later','radmanct'),'loadError'=>__('Images could not be loaded. Please try again.','radmanct'),'limit'=>__('A gallery can contain up to 100 images.','radmanct')));
 }
});
add_action('save_post',static function(int $id):void{
 if((defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE)||wp_is_post_revision($id)||!in_array(get_post_type($id),array('post','project'),true)||!current_user_can('edit_post',$id))return;
 $gallery_nonce=$_POST['rdct_gallery_nonce']??null;
 if(get_post_type($id)==='project'&&is_string($gallery_nonce)&&wp_verify_nonce(wp_unslash($gallery_nonce),'rdc-prj-gal')&&isset($_POST['rdct_project_gallery'])&&is_string($_POST['rdct_project_gallery'])){
  $gallery=gallery_ids(wp_unslash($_POST['rdct_project_gallery']));
  $gallery=array_values(array_filter($gallery,static fn($media)=>current_user_can('read_post',$media)));
  $meta=get_post_meta($id,'_webtalik_project',true);if(!is_array($meta))$meta=array();$meta['gallery']=$gallery;update_post_meta($id,'_webtalik_project',$meta);
 }
 $nonce=$_POST['rdct_media_nonce']??null;if(!is_string($nonce)||!wp_verify_nonce(wp_unslash($nonce),'rdc-card-md'))return;
 $value=$_POST['rdct_card_video']??null;if(!is_scalar($value))return;$video=video_id(wp_unslash((string)$value));if($video&&!current_user_can('read_post',$video))return;
 update_post_meta($id,'_webtalik_card_video',$video);update_post_meta($id,'_webtalik_card_mode',($_POST['rdct_card_mode']??'')==='hover'?'hover':'auto');
});
