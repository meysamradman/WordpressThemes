<?php
namespace RadmanCT\Music;
defined('ABSPATH')||exit;
function register():void {
    register_post_type('track',['labels'=>['name'=>'آهنگ‌ها','singular_name'=>'آهنگ','add_new_item'=>'افزودن آهنگ','edit_item'=>'ویرایش آهنگ','featured_image'=>'کاور آهنگ','set_featured_image'=>'انتخاب کاور'],'public'=>true,'show_in_rest'=>true,'has_archive'=>'music','rewrite'=>['slug'=>'music','with_front'=>false],'menu_icon'=>'dashicons-format-audio','supports'=>['title','editor','thumbnail','excerpt','revisions']]);
    register_taxonomy('music_category','track',['labels'=>['name'=>'دسته‌های موسیقی','singular_name'=>'دستهٔ موسیقی'],'public'=>true,'show_in_rest'=>true,'hierarchical'=>true,'show_admin_column'=>true,'rewrite'=>['slug'=>'music-category','with_front'=>false]]);
    register_post_meta('track','_rdc_track',['type'=>'object','single'=>true,'show_in_rest'=>false,'auth_callback'=>static fn($allowed,$key,$id)=>current_user_can('edit_post',(int)$id)]);
}
add_action('init',__NAMESPACE__.'\\register');
function native(?\WP_Query $q=null):bool {$q??=$GLOBALS['wp_query'];return $q->is_singular('track')||$q->is_post_type_archive('track')||$q->is_tax('music_category')||($q->is_search()&&$q->get('post_type')==='track');}
function meta(int $id):array {
    $value=get_post_meta($id,'_rdc_track',true);$value=is_array($value)?$value:[];$out=[];
    foreach(['artist','album','released','lyrics'] as $key)$out[$key]=is_scalar($value[$key]??'')?(string)($value[$key]??''):'';
    foreach(['audio','download'] as $key)$out[$key]=is_scalar($value[$key]??0)?absint($value[$key]??0):0;
    return $out;
}
function file(int $id):array {
    if(!$id||get_post_type($id)!=='attachment'||!str_starts_with((string)get_post_mime_type($id),'audio/')||!in_array(get_post_status($id),['inherit','publish'],true))return [];
    $parent=wp_get_post_parent_id($id);if($parent&&(!is_post_publicly_viewable($parent)||post_password_required($parent)))return [];
    $url=wp_get_attachment_url($id);$uploads=wp_upload_dir(null,false);if(!$url||!str_starts_with($url,trailingslashit($uploads['baseurl'])))return [];
    $metadata=wp_get_attachment_metadata($id);return ['url'=>$url,'duration'=>$metadata['length_formatted']??''];
}
function url():string {return get_post_type_archive_link('track')?:home_url('/music/');}

function card(\WP_Post $post,string $heading='h2'):void {
    $heading=in_array($heading,['h2','h3'],true)?$heading:'h2';
    $data=meta($post->ID);$allowed=!post_password_required($post);
    echo '<article class="rdc-trk-card"><a href="'.esc_url(get_permalink($post)).'">';
    if($allowed)echo get_the_post_thumbnail($post,'medium_large',['loading'=>'lazy','sizes'=>'(max-width:600px) 100vw,33vw']);
    echo '<div><'.$heading.'>'.esc_html(get_the_title($post)).'</'.$heading.'>';if($allowed&&$data['artist'])echo '<p>'.esc_html($data['artist']).'</p>';echo '<span>مشاهدهٔ آهنگ ←</span></div></a></article>';
}
add_action('add_meta_boxes_track',static function():void{
    add_meta_box('rdc-track','اطلاعات و فایل‌های آهنگ',static function(\WP_Post $post):void{
        $data=meta($post->ID);wp_nonce_field('rdc_track_save','rdc_track_nonce');
        foreach(['artist'=>'نام هنرمند','album'=>'نام آلبوم','released'=>'سال یا تاریخ انتشار'] as $key=>$label)echo '<p><label for="rdc-track-'.$key.'">'.esc_html($label).'</label><input class="widefat" id="rdc-track-'.$key.'" name="rdc_track['.$key.']" value="'.esc_attr($data[$key]).'"></p>';
        foreach(['audio'=>'فایل پخش','download'=>'فایل دانلود (اختیاری؛ پیش‌فرض همان فایل پخش)'] as $key=>$label){
            echo '<div data-rdc-audio><p>'.esc_html($label).'</p><input type="hidden" name="rdc_track['.$key.']" value="'.(int)$data[$key].'"><p data-rdc-audio-lbl>'.esc_html($data[$key]?get_the_title($data[$key]):'فایلی انتخاب نشده').'</p><button type="button" class="button" data-rdc-audio-pick>انتخاب فایل صوتی</button> <button type="button" class="button-link-delete" data-rdc-audio-clear>پاک‌کردن انتخاب</button></div>';
        }
        echo '<p><label for="rdc-track-lyrics">متن آهنگ (اختیاری)</label><textarea class="widefat" id="rdc-track-lyrics" name="rdc_track[lyrics]" rows="6">'.esc_textarea($data['lyrics']).'</textarea></p><p class="description">نام آهنگ در عنوان، تصویر در کاور و معرفی در خلاصه/ویرایشگر بومی ثبت می‌شود. فایل‌ها در همین رسانهٔ وردپرس نگه داشته می‌شوند.</p>';
    },'track','normal','high');
});
add_action('save_post_track',static function(int $id):void{
    if(wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('edit_post',$id)||!isset($_POST['rdc_track_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rdc_track_nonce'])),'rdc_track_save'))return;
    $input=isset($_POST['rdc_track'])&&is_array($_POST['rdc_track'])?wp_unslash($_POST['rdc_track']):[];$data=[];
    foreach(['artist','album','released'] as $key)$data[$key]=is_scalar($input[$key]??'')?sanitize_text_field($input[$key]??''):'';
    $data['lyrics']=is_scalar($input['lyrics']??'')?sanitize_textarea_field($input['lyrics']??''):'';
    foreach(['audio','download'] as $key){$value=is_scalar($input[$key]??0)?absint($input[$key]??0):0;$data[$key]=$value&&file($value)?$value:0;}
    update_post_meta($id,'_rdc_track',$data);
});
add_action('admin_enqueue_scripts',static function():void{
    $screen=get_current_screen();if(!$screen||$screen->base!=='post'||$screen->post_type!=='track')return;
    wp_enqueue_media();\RadmanCT\Assets\script('admin/music-editor');
});
