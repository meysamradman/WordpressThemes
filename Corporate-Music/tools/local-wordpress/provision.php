<?php
if(PHP_SAPI!=='cli'||getenv('MUSIC_LOCAL_SITE')!=='1')exit(1);
define('WP_INSTALLING',true);$_SERVER['HTTP_HOST']='127.0.0.1:8098';$_SERVER['REQUEST_URI']='/';
require '/var/www/html/wp-load.php';require_once ABSPATH.'wp-admin/includes/upgrade.php';
if(is_blog_installed()){
 wp_installing(false);if(!get_option('music_local_provisioned'))throw new RuntimeException('Existing database is not owned by Corporate Music; nothing reset.');
 echo "Existing Corporate Music data and passwords preserved.\n";exit;
}
if(!is_dir(WP_LANG_DIR))wp_mkdir_p(WP_LANG_DIR);foreach(glob('/opt/corporate-music/fixtures/languages/*') as $language)if(is_file($language))copy($language,WP_LANG_DIR.'/'.basename($language));
$password=getenv('MUSIC_ADMIN_PASSWORD');if(strlen((string)$password)<24)throw new RuntimeException('Strong password required');
$result=wp_install('رسانهٔ موسیقی',getenv('MUSIC_ADMIN_USER'),'owner@localhost.invalid',false,'',$password,'fa_IR');if(empty($result['user_id']))throw new RuntimeException('Install failed');
wp_installing(false);wp_cache_flush();wp_set_current_user($result['user_id']);update_user_meta($result['user_id'],'locale','fa_IR');
update_option('WPLANG','fa_IR');update_option('timezone_string','Asia/Tehran');update_option('blogdescription','آهنگ‌ها و مجلهٔ موسیقی');wp_update_term((int)get_option('default_category'),'category',['name'=>'دسته‌بندی‌نشده']);update_option('blog_public',0);update_option('default_comment_status','closed');
switch_theme('radmanct');require_once get_template_directory().'/inc/music.php';RadmanCT\Music\register();
require_once ABSPATH.'wp-admin/includes/image.php';require_once ABSPATH.'wp-admin/includes/media.php';
foreach(get_posts(['post_type'=>['post','page'],'post_status'=>'any','numberposts'=>-1]) as $sample)wp_delete_post($sample->ID,true);
$pages=[];foreach(['home'=>'خانه','blog'=>'وبلاگ','about-us'=>'درباره ما','contact-us'=>'تماس با ما'] as $slug=>$title){
 $content=$slug==='about-us'?'<p>این رسانه به معرفی موسیقی، هنرمندان و روایت‌های دنیای صدا می‌پردازد. اطلاعات برند و تیم در مرحلهٔ تکمیل پروژه جایگزین می‌شود.</p>':($slug==='contact-us'?'<p>اطلاعات تماس و راه‌های ارتباطی رسمی مشتری در مرحلهٔ تکمیل پروژه اینجا قرار می‌گیرد.</p>':'');
 $id=wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>$title,'post_name'=>$slug,'post_content'=>$content],true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());$pages[$slug]=$id;
}
update_option('show_on_front','page');update_option('page_on_front',$pages['home']);update_option('page_for_posts',$pages['blog']);
$manifest=json_decode(file_get_contents('/opt/corporate-music/fixtures/manifest.json'),true,512,JSON_THROW_ON_ERROR);$map=[];
foreach($manifest['media'] as $old=>$entry){$file=wp_upload_bits($entry['file'],null,file_get_contents('/opt/corporate-music/fixtures/'.$entry['file']));if($file['error'])throw new RuntimeException($file['error']);
 $id=wp_insert_attachment(['post_title'=>$entry['title'],'post_mime_type'=>$entry['mime'],'post_status'=>'inherit'],$file['file'],0,true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
 wp_update_attachment_metadata($id,wp_generate_attachment_metadata($id,$file['file']));update_post_meta($id,'_wp_attachment_image_alt',$entry['alt']);update_post_meta($id,'_rdc_demo_source','Selected public Radman Design Library demonstration media; replace for final customer content.');$map[$old]=$id;
}
foreach($manifest['posts'] as $entry){$id=wp_insert_post(['post_type'=>$entry['type'],'post_status'=>'publish','post_title'=>$entry['title'],'post_name'=>$entry['slug'],'post_content'=>str_replace('http://127.0.0.1:8097','http://127.0.0.1:8098',$entry['content']),'post_excerpt'=>$entry['excerpt'],'post_author'=>$result['user_id']],true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
 if(!empty($map[$entry['cover']]))set_post_thumbnail($id,$map[$entry['cover']]);$taxonomy=$entry['type']==='track'?'music_category':'category';
 foreach($entry['categories'] as $label){$term=term_exists($label,$taxonomy);if(!$term)$term=wp_insert_term($label,$taxonomy);if(!is_wp_error($term))wp_set_object_terms($id,[(int)$term['term_id']],$taxonomy,true);}
 if($entry['type']==='track'){$meta=$entry['music'];foreach(['audio','download'] as $key)$meta[$key]=(int)($map[$meta[$key]??0]??0);update_post_meta($id,'_rdc_track',$meta);}
}
$menu=wp_create_nav_menu('Primary');if(is_wp_error($menu))throw new RuntimeException($menu->get_error_message());
foreach([['خانه',home_url('/')],['موسیقی',home_url('/music/')],['وبلاگ',home_url('/blog/')],['درباره ما',home_url('/about-us/')],['تماس با ما',home_url('/contact-us/')]] as [$label,$url])wp_update_nav_menu_item($menu,0,['menu-item-title'=>$label,'menu-item-url'=>$url,'menu-item-type'=>'custom','menu-item-status'=>'publish']);
set_theme_mod('nav_menu_locations',['primary'=>$menu]);update_option('permalink_structure','/%postname%/');flush_rewrite_rules(true);update_option('music_local_provisioned',1,false);
echo "Independent Corporate Music WordPress installed; theme activated, selected demo music/blog and pages ready.\n";
