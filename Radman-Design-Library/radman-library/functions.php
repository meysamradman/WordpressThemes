<?php
namespace RadmanLibrary;
defined('ABSPATH')||exit;
require_once __DIR__.'/inc/modules.php';
require_once __DIR__.'/inc/sample-assets.php';
require_once __DIR__.'/inc/portfolio.php';
require_once __DIR__.'/inc/demo-query.php';
require_once __DIR__.'/inc/blog.php';
require_once __DIR__.'/inc/music.php';
require_once __DIR__.'/inc/catalog.php';
require_once __DIR__.'/inc/library.php';
add_action('after_setup_theme',static function(){
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5',['search-form','gallery','caption','style','script']);
    register_nav_menus(['primary'=>'فهرست نمونهٔ قالب','footer'=>'فهرست فوتر نمونهٔ قالب']);
});
function register_content():void {
    register_post_type('rdl_design',[
        'labels'=>['name'=>'طراحی‌ها','singular_name'=>'طراحی','add_new_item'=>'افزودن طراحی','edit_item'=>'ویرایش طراحی'],
        'public'=>true,'show_in_rest'=>true,'has_archive'=>true,'rewrite'=>['slug'=>'design'],
        'menu_icon'=>'dashicons-layout','supports'=>['title','editor','excerpt','thumbnail','revisions'],
    ]);
    register_taxonomy('rdl_group','rdl_design',[
        'labels'=>['name'=>'دسته‌های طراحی','singular_name'=>'دستهٔ طراحی'],
        'public'=>true,'show_in_rest'=>true,'hierarchical'=>true,'show_admin_column'=>true,
        'rewrite'=>['slug'=>'group'],
    ]);
    register_taxonomy('rdl_kind','rdl_design',[
        'labels'=>['name'=>'سطح طراحی','singular_name'=>'سطح طراحی'],
        'public'=>true,'show_in_rest'=>true,'hierarchical'=>true,'show_admin_column'=>true,'rewrite'=>['slug'=>'kind'],
    ]);
    register_taxonomy('rdl_fx','rdl_design',[
        'labels'=>['name'=>'فناوری و حرکت','singular_name'=>'فناوری یا حرکت'],
        'public'=>true,'show_in_rest'=>true,'hierarchical'=>true,'show_admin_column'=>true,'rewrite'=>['slug'=>'effect'],
    ]);
}
add_action('init',__NAMESPACE__.'\\register_content');
add_action('pre_get_posts',static function(\WP_Query $query){
    if(is_admin()||!$query->is_main_query())return;
    if(Music\native($query)||Catalog\native($query))return;
    if(Blog\native($query)){if($query->get('rdl_blog'))$query->set('post_type','post');return;}
    if($query->is_home()||$query->is_post_type_archive('rdl_design')||$query->is_tax(['rdl_group','rdl_kind','rdl_fx'])||$query->is_search()){
        $query->set('post_type','rdl_design');
        $query->set('posts_per_page',12);
        $query->set('orderby',['date'=>'DESC','ID'=>'DESC']);
    }
});
add_action('wp_enqueue_scripts',static function(){
    if(Samples\raw())return;
    $root=get_template_directory();$uri=get_template_directory_uri();
    wp_enqueue_style('rdl-shell',$uri.'/assets/shell.css',[],filemtime($root.'/assets/shell.css'));
    wp_enqueue_style('rdl-bar',$uri.'/assets/bar.css',['rdl-shell'],filemtime($root.'/assets/bar.css'));
    if(!is_singular('rdl_design')||post_password_required())return;
    $module=module(get_queried_object_id());
    if(!$module||isset($module['source']))return;
    $dir='/modules/'.$module['slug'].'/';
    if(is_file($root.$dir.'style.css'))wp_enqueue_style('rdl-sample',$uri.$dir.'style.css',['rdl-shell'],filemtime($root.$dir.'style.css'));
    if(is_file($root.$dir.'script.js'))wp_enqueue_script('rdl-sample',$uri.$dir.'script.js',[],filemtime($root.$dir.'script.js'),['in_footer'=>true,'strategy'=>'defer']);
});
add_action('add_meta_boxes_rdl_design',static function(){
    add_meta_box('rdl-module','ماژول پیش‌نمایش',static function(\WP_Post $post){
        wp_nonce_field('rdl_module_save','rdl_module_nonce');
        echo '<label for="rdl-module-select">طراحی ثبت‌شده</label><select style="width:100%" id="rdl-module-select" name="rdl_module"><option value="">محتوای ویرایشگر</option>';
        foreach(modules() as $slug=>$item)echo '<option value="'.esc_attr($slug).'" '.selected(get_post_meta($post->ID,'_rdl_module',true),$slug,false).'>'.esc_html($item['title']).'</option>';
        echo '</select><p>هر ماژول فقط در پیش‌نمایش خودش اجرا می‌شود.</p>';
    },'rdl_design','side');
});
add_action('save_post_rdl_design',static function(int $id){
    if(wp_is_post_revision($id)||wp_is_post_autosave($id)||!current_user_can('edit_post',$id))return;
    if(!isset($_POST['rdl_module_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rdl_module_nonce'])),'rdl_module_save'))return;
    $slug=isset($_POST['rdl_module'])?sanitize_key(wp_unslash($_POST['rdl_module'])):'';
    if($slug===''||isset(modules()[$slug]))update_post_meta($id,'_rdl_module',$slug);
});
