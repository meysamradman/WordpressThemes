<?php
require '/var/www/html/wp-load.php';
require_once ABSPATH.'wp-admin/includes/plugin.php';
if(DB_NAME!=='store_radman_local'||!get_option('store_radman_local_owner'))throw new RuntimeException('Wrong environment');
$errors=[];$plugins=[];
foreach(['woocommerce/woocommerce.php','wordpress-seo/wp-seo.php','all-in-one-wp-migration/all-in-one-wp-migration.php'] as $file){$plugins[$file]=is_plugin_active($file);if(!$plugins[$file])$errors[]=$file;}
$admin=get_user_by('login','radman');wp_set_current_user($admin->ID);
$pages=[];foreach(['shop','cart','checkout','myaccount'] as $name){$id=wc_get_page_id($name);$pages[$name]=get_permalink($id);if($id<1||get_post_status($id)!=='publish')$errors[]=$name;}
if(!current_user_can('install_plugins')||!current_user_can('update_plugins')||!is_writable(WP_PLUGIN_DIR))$errors[]='Plugin installation permission';
if(post_type_exists('project')||get_stylesheet()!=='radmanstore')$errors[]='Theme scope';
if((int)get_option('blog_public')!==0)$errors[]='Local index';
echo wp_json_encode(['ok'=>!$errors,'errors'=>$errors,'mysql'=>$wpdb->get_var('SELECT VERSION()'),'wordpress'=>get_bloginfo('version'),'php'=>PHP_VERSION,'plugins'=>$plugins,'pages'=>$pages,'manual_plugin_install_and_update'=>true,'hpos'=>get_option('woocommerce_custom_orders_table_enabled'),'portfolio'=>post_type_exists('project')],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit($errors?1:0);
