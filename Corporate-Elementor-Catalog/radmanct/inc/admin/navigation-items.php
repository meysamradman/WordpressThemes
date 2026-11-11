<?php

namespace RadmanCT\Admin\NavigationItems;

defined( 'ABSPATH' ) || exit;

function field( $item_id, $item ): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) { return; }
	$id = (int) $item_id;
	$value = \RadmanCT\Navigation\submenu_type( $id, '' );
	echo '<p class="description description-wide"><label for="rdct-submenu-' . $id . '">' . esc_html__( 'Radman CT top-level submenu', 'radmanct' ) . '<br><select class="widefat" id="rdct-submenu-' . $id . '" name="rdct_submenu_type[' . $id . ']">';
	foreach ( array( '' => __( 'Inherit menu setting', 'radmanct' ), 'dropdown' => __( 'Dropdown', 'radmanct' ), 'mega' => __( 'Mega menu', 'radmanct' ) ) as $key => $title ) {
		echo '<option value="' . esc_attr( $key ) . '"' . selected( $value, $key, false ) . '>' . esc_html( $title ) . '</option>';
	}
	echo '</select></label></p>';
	require_once dirname( __DIR__ ) . '/navigation/presentation.php';
	$block_id = \RadmanCT\Navigation\MegaContent\block( $id );
	echo '<p class="description description-wide"><label>' . esc_html__( 'Mega menu content block', 'radmanct' ) . '<br><select class="widefat" name="rdct_mega_block[' . $id . ']">';
	foreach ( \RadmanCT\Navigation\Presentation\blocks() as $key => $title ) { echo '<option value="' . (int) $key . '"' . selected( $block_id, $key, false ) . '>' . esc_html( $title ) . '</option>'; }
	echo '</select></label></p>';
	wp_nonce_field( 'rdct-menu-item-' . $id, 'rdct_menu_item_nonce[' . $id . ']', false );
}

function save( int $menu_id, int $item_id ): void {
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || ! current_user_can( 'edit_theme_options' ) || 'nav_menu_item' !== get_post_type( $item_id ) ) { return; }
	$values = $_POST['rdct_submenu_type'] ?? null;
	$nonces = $_POST['rdct_menu_item_nonce'] ?? null;
	if ( ! is_array( $values ) || ! is_array( $nonces ) || ! isset( $values[ $item_id ], $nonces[ $item_id ] ) || ! is_string( $values[ $item_id ] ) || ! is_string( $nonces[ $item_id ] ) || ! wp_verify_nonce( $nonces[ $item_id ], 'rdct-menu-item-' . $item_id ) ) { return; }
	$value = wp_unslash( $values[ $item_id ] );
	if ( '' !== $value && ! in_array( $value, \RadmanCT\Navigation\SUBMENU_STYLES, true ) ) { return; }
	if ( '' === $value ) { delete_post_meta( $item_id, \RadmanCT\Navigation\SUBMENU_META ); }
	else { update_post_meta( $item_id, \RadmanCT\Navigation\SUBMENU_META, $value ); }
	$blocks = $_POST['rdct_mega_block'] ?? array();
	if ( is_array( $blocks ) && isset( $blocks[ $item_id ] ) && is_string( $blocks[ $item_id ] ) && ctype_digit( $blocks[ $item_id ] ) && strlen( $blocks[ $item_id ] ) < 19 ) {
		$block_id = (int) $blocks[ $item_id ];
		if ( $block_id ) { update_post_meta( $item_id, \RadmanCT\Navigation\MegaContent\META, $block_id ); }
		else { delete_post_meta( $item_id, \RadmanCT\Navigation\MegaContent\META ); }
	}
}

add_action( 'wp_nav_menu_item_custom_fields', __NAMESPACE__ . '\\field', 10, 2 );
add_action( 'wp_update_nav_menu_item', __NAMESPACE__ . '\\save', 10, 2 );
