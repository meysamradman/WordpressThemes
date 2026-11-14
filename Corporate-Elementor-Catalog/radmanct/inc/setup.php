<?php

namespace RadmanCT\Setup;

defined( 'ABSPATH' ) || exit;

function register(): void {
	load_theme_textdomain( 'radmanct', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'custom-logo', array( 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	register_nav_menus( array( 'primary' => __( 'Primary menu', 'radmanct' ), 'footer' => __( 'Footer menu', 'radmanct' ) ) );
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\\register' );

add_action( 'customize_register', static function ( $manager ): void {
	$control = $manager->get_control( 'custom_logo' );
	if ( ! $control || ! class_exists( '\\WP_Customize_Media_Control' ) ) { return; }
	$manager->remove_control( 'custom_logo' );
	$manager->add_control( new \WP_Customize_Media_Control( $manager, 'custom_logo', array(
		'section' => $control->section,
		'priority' => $control->priority,
		'settings' => 'custom_logo',
		'label' => __( 'Site logo', 'radmanct' ),
		'description' => __( 'The default logo for the site. Header and footer elements can override it per device.', 'radmanct' ),
		'mime_type' => 'image',
	) ) );
}, 20 );

