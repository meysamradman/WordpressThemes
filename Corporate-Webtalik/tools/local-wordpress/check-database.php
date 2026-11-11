<?php
// Only a disposable database may run this write smoke check.
if (PHP_SAPI !== 'cli' || getenv('RDCT_DB_CHECK') !== '1' || getenv('WORDPRESS_DB_NAME') !== 'rdct_check') { exit(1); }
define('WP_HOME','http://rdc-check.invalid');
define('WP_SITEURL','http://rdc-check.invalid');
define('WP_ENVIRONMENT_TYPE','local');
define('WP_HTTP_BLOCK_EXTERNAL',true);
define('DISABLE_WP_CRON',true);
define('AUTOMATIC_UPDATER_DISABLED',true);
define('WP_DEBUG_LOG',false);
define('WP_DEBUG_DISPLAY',true);
if (getenv('RDCT_DB_INSTALL') === '1') { define('WP_INSTALLING',true); }
$_SERVER['HTTP_HOST']='rdc-check.invalid';
$_SERVER['REQUEST_URI']='/';
require '/var/www/html/wp-load.php';
function check_database($condition,string $message):void { if (!$condition) { throw new RuntimeException($message); } }
add_filter('pre_wp_mail','__return_true');
if (getenv('RDCT_DB_INSTALL') === '1') {
 check_database(!is_blog_installed(),'Refusing to install over an existing database');
 require_once ABSPATH.'wp-admin/includes/upgrade.php';
 $result=wp_install('Radman CT check','rdct_check','check@localhost.invalid',false,'',getenv('WORDPRESS_DB_PASSWORD'),'en_US');
 check_database(!empty($result['user_id']),'WordPress installation failed');
 wp_installing(false);
 switch_theme('radmanct');
 echo "Disposable WordPress installation ready.\n";
 exit;
}
global $wpdb;
check_database(DB_NAME === 'rdct_check','Unexpected database');
check_database(wp_get_theme()->get('Name') === 'Radman CT','Theme activation failed');
check_database(post_type_exists('project') && taxonomy_exists('project_category'),'Project registration failed');
$admins=get_users(array('role'=>'administrator','fields'=>'ID','number'=>1));
wp_set_current_user((int)$admins[0]);
$post=wp_insert_post(array('post_type'=>'project','post_status'=>'draft','post_title'=>'آزمون فارسی — نمونه‌کار','post_content'=>'متن فارسی و نیم‌فاصله'),true);
check_database(!is_wp_error($post),'Project insert failed');
check_database(get_the_title($post) === 'آزمون فارسی — نمونه‌کار','UTF-8 text round trip failed');
$term=term_exists('شرکتی','project_category') ?: wp_insert_term('شرکتی','project_category');
check_database(!is_wp_error($term),'Category insert failed');
wp_set_object_terms($post,array($term['term_id']),'project_category');
$images=array();
foreach(array('first','second') as $name) {
 $id=wp_insert_attachment(array('post_title'=>$name,'post_mime_type'=>'image/jpeg','post_status'=>'inherit','guid'=>WP_HOME.'/uploads/'.$name.'.jpg'),false,$post,true);
 check_database(!is_wp_error($id),'Attachment insert failed');
 update_post_meta($id,'_wp_attached_file',$name.'.jpg');
 update_post_meta($id,'_wp_attachment_metadata',array('width'=>1200,'height'=>800,'file'=>$name.'.jpg'));
 $images[]=$id;
}
$video=wp_insert_attachment(array('post_title'=>'Video','post_mime_type'=>'video/mp4','post_status'=>'inherit','guid'=>WP_HOME.'/uploads/video.mp4'),false,$post,true);
check_database(!is_wp_error($video),'Video insert failed');
update_post_meta($video,'_wp_attached_file','video.mp4');
update_post_meta($post,'_webtalik_project',array('client'=>'Keep this field','gallery'=>$images));
$_POST=array('rdct_gallery_nonce'=>wp_create_nonce('rdc-prj-gal'),'rdct_project_gallery'=>implode(',',array_reverse($images)),'rdct_media_nonce'=>wp_create_nonce('rdc-card-md'),'rdct_card_video'=>(string)$video,'rdct_card_mode'=>'hover');
do_action('save_post',$post,get_post($post),true);
$meta=get_post_meta($post,'_webtalik_project',true);
check_database($meta['gallery'] === array_reverse($images),'Gallery order round trip failed');
check_database($meta['client'] === 'Keep this field','Unrelated metadata overwritten');
check_database((int)get_post_meta($post,'_webtalik_card_video',true) === $video,'Video metadata round trip failed');
check_database(get_post_meta($post,'_webtalik_card_mode',true) === 'hover','Preview mode round trip failed');
$_POST=array('rdct_gallery_nonce'=>'invalid','rdct_project_gallery'=>'');
do_action('save_post',$post,get_post($post),true);
check_database(get_post_meta($post,'_webtalik_project',true) === $meta,'Invalid nonce was accepted');
$_POST=array('rdct_gallery_nonce'=>wp_create_nonce('rdc-prj-gal'),'rdct_project_gallery'=>'');
do_action('save_post',$post,get_post($post),true);
check_database(get_post_meta($post,'_webtalik_project',true)['gallery'] === array(),'Gallery clear failed');
$_POST=array();
$query=new WP_Query(array('post_type'=>'project','post_status'=>'draft','tax_query'=>array(array('taxonomy'=>'project_category','terms'=>array($term['term_id'])))));
check_database(in_array($post,wp_list_pluck($query->posts,'ID'),true),'Project taxonomy query failed');
update_option('rdct_check_option',array('text'=>'دادهٔ فارسی'));
check_database(get_option('rdct_check_option')['text'] === 'دادهٔ فارسی','Option round trip failed');
check_database($wpdb->last_error === '','Database reported a query error');
echo 'PASS: WordPress '.get_bloginfo('version').'; database '.$wpdb->db_version().'; prefix '.$wpdb->prefix.'.'.PHP_EOL;
echo "PASS: theme activation, UTF-8 project CRUD, taxonomy queries, ordered gallery, preserved metadata, video preview, nonce rejection, explicit clear and options.\n";
