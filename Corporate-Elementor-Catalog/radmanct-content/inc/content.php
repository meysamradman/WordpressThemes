<?php

namespace RadmanCTContent\Content;

defined( 'ABSPATH' ) || exit;

function register(): void {
	if ( \RadmanCTContent\Modules\enabled( 'catalog' ) ) {
		register_type( 'radman_product', __( 'Products', 'radmanct-content' ), __( 'Product', 'radmanct-content' ), 'products', 'dashicons-products' );
		register_taxonomy( 'radman_product_cat', 'radman_product', taxonomy_args( __( 'Product categories', 'radmanct-content' ), __( 'Product category', 'radmanct-content' ), 'product-category', true ) );
		register_taxonomy( 'radman_product_brand', 'radman_product', taxonomy_args( __( 'Brands', 'radmanct-content' ), __( 'Brand', 'radmanct-content' ), 'brand', false ) );
		register_taxonomy( 'radman_product_feature', 'radman_product', taxonomy_args( __( 'Product features', 'radmanct-content' ), __( 'Product feature', 'radmanct-content' ), 'product-feature', false ) );
		register_taxonomy( 'radman_product_tag', 'radman_product', taxonomy_args( __( 'Tags', 'default' ), __( 'Tag', 'default' ), 'product-tag', false ) );
	}
	if ( \RadmanCTContent\Modules\enabled( 'portfolio' ) ) {
		register_type( 'radman_portfolio', __( 'Projects', 'radmanct-content' ), __( 'Project', 'radmanct-content' ), 'projects', 'dashicons-portfolio' );
		register_taxonomy( 'radman_portfolio_cat', 'radman_portfolio', taxonomy_args( __( 'Project categories', 'radmanct-content' ), __( 'Project category', 'radmanct-content' ), 'project-category', true ) );
	}
}

function register_type( string $type, string $plural, string $singular, string $slug, string $icon ): void {
	$args = array(
		'labels' => array(
			'name' => $plural, 'singular_name' => $singular, 'all_items' => $plural,
			'add_new_item' => sprintf( __( 'Add %s', 'radmanct-content' ), $singular ),
			'edit_item' => sprintf( __( 'Edit %s', 'radmanct-content' ), $singular ),
			'view_item' => sprintf( __( 'View %s', 'radmanct-content' ), $singular ),
			'view_items' => sprintf( __( 'View %s', 'radmanct-content' ), $plural ),
			'search_items' => sprintf( __( 'Search %s', 'radmanct-content' ), $plural ),
			'new_item' => sprintf( __( 'New %s', 'radmanct-content' ), $singular ),
			'not_found' => __( 'No items found.', 'radmanct-content' ),
			'not_found_in_trash' => __( 'No items found in Trash.', 'radmanct-content' ),
		),
		'public' => true, 'show_in_rest' => true, 'has_archive' => true, 'rewrite' => array( 'slug' => $slug, 'with_front' => false ),
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ), 'menu_icon' => $icon, 'map_meta_cap' => true,
	);
	register_post_type( $type, apply_filters( 'radman_core/content/post_type_args', $args, $type ) );
	\RadmanCTContent\Model\register( $type );
}

function taxonomy_args( string $label, string $singular, string $slug, bool $hierarchical ): array {
	return array(
		'label' => $label,
		'labels' => array(
			'name' => $label, 'singular_name' => $singular, 'all_items' => $label,
			'search_items' => sprintf( __( 'Search %s', 'radmanct-content' ), $label ),
			'edit_item' => sprintf( __( 'Edit %s', 'radmanct-content' ), $singular ),
			'view_item' => sprintf( __( 'View %s', 'radmanct-content' ), $singular ),
			'update_item' => sprintf( __( 'Update %s', 'radmanct-content' ), $singular ),
			'add_new_item' => sprintf( __( 'Add %s', 'radmanct-content' ), $singular ),
			'new_item_name' => sprintf( __( 'New %s', 'radmanct-content' ), $singular ),
		),
		'public' => true, 'hierarchical' => $hierarchical, 'show_in_rest' => true, 'show_admin_column' => true, 'rewrite' => array( 'slug' => $slug, 'with_front' => false ),
	);
}
