<?php
namespace RadmanCT\Content\Projects;
defined( 'ABSPATH' ) || exit;
function register(): void {
    register_post_type( 'project', array(
        'labels' => array( 'name' => __( 'Projects', 'radmanct' ), 'singular_name' => __( 'Project', 'radmanct' ) ),
        'public' => true, 'show_in_rest' => true, 'has_archive' => 'projects', 'rewrite' => array( 'slug' => 'projects', 'with_front' => false ),
        'menu_icon' => 'dashicons-portfolio', 'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
    ) );
    register_taxonomy( 'project_category', 'project', array(
        'labels' => array( 'name' => __( 'Project categories', 'radmanct' ), 'singular_name' => __( 'Project category', 'radmanct' ) ),
        'public' => true, 'hierarchical' => true, 'show_in_rest' => true, 'rewrite' => array( 'slug' => 'project-category', 'with_front' => false ),
    ) );
}
add_action( 'init', __NAMESPACE__ . '\register' );
add_action('after_switch_theme',static function():void{register();flush_rewrite_rules();});
