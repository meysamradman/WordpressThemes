<?php
if(PHP_SAPI!=='cli')exit(1);
define('WP_INSTALLING',true);
$_SERVER['HTTP_HOST']='127.0.0.1:8097';
$_SERVER['REQUEST_URI']='/';
require '/var/www/html/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
if(is_blog_installed()){
    if(!get_option('rdl_local_ready'))throw new RuntimeException('Existing installation preserved; ownership marker missing.');
    echo "Library already installed; data and passwords preserved.\n";
    exit;
}
$password=getenv('LIBRARY_ADMIN_PASSWORD');
if(!$password)throw new RuntimeException('Missing administrator password.');
$result=wp_install('Radman · کتابخانه طراحی','admin','radman@localhost.invalid',false,'',$password,'en_US');
if(empty($result['user_id']))throw new RuntimeException('Install failed.');
wp_installing(false);
wp_cache_flush();
wp_set_current_user($result['user_id']);
update_option('timezone_string','Asia/Tehran');
update_option('WPLANG','fa_IR');
update_user_meta($result['user_id'],'locale','fa_IR');
update_option('blogdescription','کتابخانهٔ مستقل طراحی و حرکت');
update_option('blog_public',0);
update_option('permalink_structure','/%postname%/');
$GLOBALS['wp_rewrite']->init();
switch_theme('radman-library');
require_once get_template_directory().'/functions.php';
\RadmanLibrary\register_content();
foreach(['hero'=>'هیرو','page-title'=>'عنوان صفحه','panels'=>'پنل‌های اسکرولی','showcase'=>'نمایش تعاملی','accordion'=>'آکاردئون','gallery'=>'گالری','motion'=>'متن و حرکت','header'=>'هدر','footer'=>'فوتر'] as $slug=>$name){
    $term=wp_insert_term($name,'rdl_group',['slug'=>$slug]);
    if(is_wp_error($term))throw new RuntimeException($term->get_error_message());
}
$id=wp_insert_post(['post_type'=>'rdl_design','post_status'=>'publish','post_title'=>'شروع یک طراحی مستقل','post_name'=>'starter','post_excerpt'=>'نمونهٔ پایه برای پیش‌نمایش و ساخت ماژول‌های بعدی؛ بدون انیمیشن و وابستگی اضافی.'],true);
if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
update_post_meta($id,'_rdl_module','starter');
wp_set_object_terms($id,'hero','rdl_group');
flush_rewrite_rules(true);
update_option('rdl_local_ready',1,false);
echo "Independent library installed. No customer content or plugins copied.\n";
