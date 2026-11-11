<?php
namespace RadmanCT;
defined( 'ABSPATH' ) || exit;
add_action( 'after_setup_theme', static function (): void {
    load_theme_textdomain( 'radmanct', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_post_type_support( 'page', 'excerpt' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    register_nav_menus( array( 'primary' => __( 'Primary menu', 'radmanct' ), 'footer' => __( 'Footer menu', 'radmanct' ) ) );
} );

add_action('pre_get_posts',static function(\WP_Query $query):void{
 if(is_admin()||!$query->is_main_query()||$query->is_feed())return;
 if($query->is_search())$query->set('post_type','post');
 if($query->is_post_type_archive('project')||$query->is_tax('project_category'))$query->set('posts_per_page',8);
});

