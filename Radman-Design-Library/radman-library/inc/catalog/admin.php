<?php
namespace RadmanLibrary\Catalog;
defined('ABSPATH')||exit;
add_action('add_meta_boxes_radman_product',static function():void{add_meta_box('rdl-prd','اطلاعات کاتالوگ',static function(\WP_Post $post):void{
 $data=Model\read($post->ID);wp_nonce_field('rdl_prd_save','rdl_prd_nonce');require __DIR__.'/fields.php';
},'radman_product','normal','high');});
add_action('save_post_radman_product',static function(int $id):void{
 if(wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('edit_post',$id)||!isset($_POST['rdl_prd_nonce'])||!is_string($_POST['rdl_prd_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rdl_prd_nonce'])),'rdl_prd_save'))return;
 $input=isset($_POST['rdl_prd'])&&is_array($_POST['rdl_prd'])?wp_unslash($_POST['rdl_prd']):[];
 if(!is_string($input['gallery']??null)||!is_array($input['specifications']??[]))return;
 $input['version']=1;$input['gallery']=array_values(array_unique(array_filter(array_map('absint',explode(',',$input['gallery'])))));$rows=[];
 foreach(($input['specifications']??[]) as $row){if(!is_array($row))return;if(($row['label']??'')===''&&($row['value']??'')==='')continue;$rows[]=$row;}
 $input['specifications']=$rows;$data=Model\validate($input);if(!$data)return;
 $data['gallery']=array_values(array_filter($data['gallery'],'wp_attachment_is_image'));update_post_meta($id,Model\META,$data);
});
add_action('admin_enqueue_scripts',static function():void{$screen=get_current_screen();if(!$screen||$screen->base!=='post'||$screen->post_type!=='radman_product')return;wp_enqueue_media();wp_enqueue_script('rdl-prd-ed',get_template_directory_uri().'/src/js/admin/catalog-editor.js',['media-editor'],filemtime(get_template_directory().'/src/js/admin/catalog-editor.js'),true);});
