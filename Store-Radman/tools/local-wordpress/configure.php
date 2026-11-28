<?php
if ('1' !== getenv('STORE_LOCAL') || 'cli' !== PHP_SAPI) { exit(1); }
require '/var/www/html/wp-load.php';
if (DB_NAME !== 'store_radman_local' || !get_option('store_radman_local_owner')) { throw new RuntimeException('Wrong environment.'); }
if (!class_exists('WooCommerce')) { throw new RuntimeException('WooCommerce missing.'); }
wp_set_current_user(get_user_by('login', 'radman')->ID);
if (!get_option('store_radman_configured')) {
    switch_theme('radmanstore');
    WC_Install::create_pages();
    $pages=[];
    foreach (['home'=>'خانه','blog'=>'وبلاگ'] as $slug=>$title) {
        $existing=get_page_by_path($slug);
        $id=$existing?$existing->ID:wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>$title,'post_name'=>$slug],true);
        if (is_wp_error($id)) { throw new RuntimeException($id->get_error_message()); }
        $pages[$slug]=$id;
    }
    update_option('show_on_front','page');
    update_option('page_on_front',$pages['home']);
    update_option('page_for_posts',$pages['blog']);
    update_option('blogdescription','');
    update_option('blog_public',0);
    update_option('permalink_structure','/%postname%/');
    update_option('woocommerce_default_country','IR');
    update_option('woocommerce_currency','IRR');
    update_option('woocommerce_allow_tracking','no');
    update_option('woocommerce_coming_soon','no');
    update_option('woocommerce_store_pages_only','no');
    update_option('woocommerce_onboarding_profile',['completed'=>true,'skipped'=>true]);
    $menu=wp_create_nav_menu('فهرست اصلی');
    if (is_wp_error($menu)) { throw new RuntimeException($menu->get_error_message()); }
    foreach ([$pages['home'],get_option('woocommerce_shop_page_id'),$pages['blog'],get_option('woocommerce_myaccount_page_id'),get_option('woocommerce_cart_page_id')] as $id) {
        wp_update_nav_menu_item($menu,0,['menu-item-object-id'=>$id,'menu-item-object'=>'page','menu-item-type'=>'post_type','menu-item-status'=>'publish']);
    }
    set_theme_mod('nav_menu_locations',['primary'=>$menu]);
    flush_rewrite_rules(true);
    update_option('store_radman_configured',1,false);
}
require_once ABSPATH.'wp-admin/includes/translation-install.php';
wp_download_language_pack('fa_IR');
$plugins=get_plugins();$versions=[];
foreach (get_option('active_plugins',[]) as $file) { $versions[$file]=$plugins[$file]['Version']??''; }
echo wp_json_encode(['wordpress'=>get_bloginfo('version'),'php'=>PHP_VERSION,'plugins'=>$versions,'theme'=>get_stylesheet(),'shop'=>get_permalink(wc_get_page_id('shop')),'cart'=>get_permalink(wc_get_page_id('cart')),'checkout'=>get_permalink(wc_get_page_id('checkout')),'account'=>get_permalink(wc_get_page_id('myaccount')),'public'=>get_option('blog_public'),'portfolio'=>post_type_exists('project')],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
