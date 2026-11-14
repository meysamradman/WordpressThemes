<?php
namespace RadmanLibrary\Collections;
defined('ABSPATH')||exit;
add_action('init',static function():void{
 foreach(['album'=>['آلبوم‌های عکس','آلبوم عکس','gallery','dashicons-format-gallery'],'clip'=>['ویدئوها','ویدئو','videos','dashicons-video-alt3']] as $type=>[$plural,$single,$slug,$icon]){
  register_post_type($type,['labels'=>['name'=>$plural,'singular_name'=>$single,'add_new_item'=>'افزودن '.$single,'edit_item'=>'ویرایش '.$single],'public'=>true,'show_in_rest'=>true,'has_archive'=>$slug,'rewrite'=>['slug'=>$slug,'with_front'=>false],'menu_icon'=>$icon,'supports'=>['title','editor','thumbnail','excerpt','revisions','author']]);
  foreach(['category'=>true,'tag'=>false] as $suffix=>$hierarchical)register_taxonomy($type.'_'.$suffix,$type,['label'=>($hierarchical?'دسته‌های ':'برچسب‌های ').$single,'public'=>true,'show_in_rest'=>true,'hierarchical'=>$hierarchical,'show_admin_column'=>true,'rewrite'=>['slug'=>$slug.'-'.$suffix,'with_front'=>false]]);
  register_post_meta($type,'_rdl_'.$type,['type'=>'object','single'=>true,'show_in_rest'=>false,'auth_callback'=>static fn($allowed,$key,$id)=>current_user_can('edit_post',(int)$id)]);
 }
});
function native(?\WP_Query $q=null):bool {$q??=$GLOBALS['wp_query'];return $q->is_singular(['album','clip'])||$q->is_post_type_archive(['album','clip'])||$q->is_tax(['album_category','album_tag','clip_category','clip_tag']);}
function images(int $id):array {
 if(post_password_required($id))return [];$data=get_post_meta($id,'_rdl_album',true);
 return array_values(array_filter(\RadmanCT\Content\Media\gallery_ids(is_array($data)?($data['images']??[]):[]),static fn($image)=>\RadmanCT\PageTitle\attachment_allowed($image)));
}
function file(int $id):array {
 if(!\RadmanCT\PageTitle\attachment_allowed($id)||!in_array(get_post_mime_type($id),['video/mp4','video/webm'],true))return [];
 $url=wp_get_attachment_url($id);$uploads=wp_upload_dir(null,false);return $url&&str_starts_with($url,trailingslashit($uploads['baseurl']))?['url'=>$url,'type'=>get_post_mime_type($id)]:[];
}
function clip(int $id):array {if(post_password_required($id))return [];$data=get_post_meta($id,'_rdl_clip',true);return is_array($data)?$data:[];}
function render(string $type,string $layout,string $template):void {
 $previous=$GLOBALS['wp_query'];$post=$GLOBALS['post']??null;$design=get_queried_object_id();$demo=!native();
 if($demo){$query=['post_type'=>$type,'post_status'=>'publish','has_password'=>false,'posts_per_page'=>$type==='clip'?4:16,'paged'=>max(1,is_scalar($_GET['rdl_pg']??1)?absint($_GET['rdl_pg']??1):1)];if($layout==='single'){$ids=get_posts(['post_type'=>$type,'post_status'=>'publish','has_password'=>false,'posts_per_page'=>1,'fields'=>'ids']);if(!$ids){echo '<p>هنوز محتوایی اضافه نشده است.</p>';return;}$query['p']=$ids[0];unset($query['paged']);}$GLOBALS['wp_query']=new \WP_Query($query);}
 $link=static fn($url,$page)=>add_query_arg('rdl_pg',(int)$page,get_permalink($design));if($demo)add_filter('get_pagenum_link',$link,10,2);
 try{require $template;}finally{if($demo)remove_filter('get_pagenum_link',$link,10);$GLOBALS['wp_query']=$previous;$GLOBALS['post']=$post;if($post)setup_postdata($post);}
}
add_action('pre_get_posts',static function(\WP_Query $q):void{if(!is_admin()&&$q->is_main_query()&&native($q)&&!$q->is_singular())$q->set('posts_per_page',$q->is_post_type_archive('clip')||$q->is_tax(['clip_category','clip_tag'])?4:16);});
require_once __DIR__.'/collections/editor.php';
