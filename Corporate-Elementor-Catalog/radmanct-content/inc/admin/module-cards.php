<?php

namespace RadmanCTContent\Admin\ModuleCards;

defined( 'ABSPATH' ) || exit;

function render( array $values ): void {
	$modules = array(
		'catalog' => array( 'title' => __( 'Product catalog', 'radmanct-content' ), 'type' => 'radman_product', 'icon' => 'products', 'description' => __( 'Products, categories, brands, features, galleries and technical specifications. No cart or checkout.', 'radmanct-content' ) ),
		'portfolio' => array( 'title' => __( 'Portfolio', 'radmanct-content' ), 'type' => 'radman_portfolio', 'icon' => 'portfolio', 'description' => __( 'Projects, categories and image galleries. Independent of the product catalog.', 'radmanct-content' ) ),
	);
	echo '<div class="rdct-cor-modules">';
	foreach ( $modules as $key => $module ) {
		$enabled = $values[ $key ];
		echo '<section class="rdct-cor-module"><header><span class="dashicons dashicons-' . esc_attr( $module['icon'] ) . '" aria-hidden="true"></span><h3>' . esc_html( $module['title'] ) . '</h3><span class="rdct-cor-module__status">' . ( $enabled ? esc_html__( 'Enabled', 'radmanct-content' ) : esc_html__( 'Disabled', 'radmanct-content' ) ) . '</span></header><p>' . esc_html( $module['description'] ) . '</p>';
		echo '<label><input type="hidden" name="radman_core_modules[' . esc_attr( $key ) . ']" value="0"><input type="checkbox" name="radman_core_modules[' . esc_attr( $key ) . ']" value="1"' . checked( $enabled, true, false ) . '> ' . esc_html__( 'Enable this module', 'radmanct-content' ) . '</label>';
		if ( $enabled && post_type_exists( $module['type'] ) ) {
			$type = get_post_type_object( $module['type'] );
			echo '<div class="rdct-cor-module__actions">';
			if ( $type && current_user_can( $type->cap->edit_posts ) ) {
				echo '<a class="button" href="' . esc_url( admin_url( 'edit.php?post_type=' . $module['type'] ) ) . '">' . esc_html__( 'Manage items', 'radmanct-content' ) . '</a>';
			}
			if ( $type && current_user_can( $type->cap->create_posts ) ) {
				echo '<a class="button" href="' . esc_url( admin_url( 'post-new.php?post_type=' . $module['type'] ) ) . '">' . esc_html__( 'Add item', 'radmanct-content' ) . '</a>';
			}
			$archive = get_post_type_archive_link( $module['type'] );
			if ( $archive ) { echo '<a class="button" href="' . esc_url( $archive ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'View archive', 'radmanct-content' ) . '</a>'; }
			echo '</div>';
		}
		echo '</section>';
	}
	echo '</div>';
}
