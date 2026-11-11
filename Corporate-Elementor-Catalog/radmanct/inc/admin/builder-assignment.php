<?php

namespace RadmanCT\Admin\BuilderAssignment;

defined( 'ABSPATH' ) || exit;

function register(): void {
	if ( current_user_can( 'edit_theme_options' ) ) {
		add_meta_box( 'rdct-layouts', __( 'Radman CT header and footer', 'radmanct' ), __NAMESPACE__ . '\\render', 'page', 'side' );
	}
}

function render( \WP_Post $post ): void {
	wp_nonce_field( 'radman_page_layouts', 'radman_layout_nonce' );
	foreach ( array( 'header', 'footer' ) as $kind ) {
		$current = get_post_meta( $post->ID, '_radman_' . $kind . '_layout', true );
		$title = 'header' === $kind ? __( 'Header builder', 'radmanct' ) : __( 'Footer builder', 'radmanct' );
		echo '<p><label for="rdct-' . $kind . '-layout">' . esc_html( $title ) . '</label><select style="width:100%" id="rdct-' . $kind . '-layout" name="radman_page_layout[' . $kind . ']">';
		$items = array_merge( array( array( 'id' => '', 'title' => __( 'Inherit site or language default', 'radmanct' ) ), array( 'id' => 'none', 'title' => __( 'Hide on this page', 'radmanct' ) ) ), \RadmanCT\Builder\Storage\listing( $kind ) );
		foreach ( $items as $item ) {
			echo '<option value="' . esc_attr( $item['id'] ) . '"' . selected( $current, $item['id'], false ) . '>' . esc_html( $item['title'] ) . '</option>';
		}
		echo '</select></p>';
	}
}

function save( int $post_id ): void {
	$nonce = $_POST['radman_layout_nonce'] ?? null;
	$values = $_POST['radman_page_layout'] ?? null;
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! is_string( $nonce ) || ! wp_verify_nonce( $nonce, 'radman_page_layouts' ) || ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'edit_post', $post_id ) || ! is_array( $values ) ) {
		return;
	}
	foreach ( array( 'header', 'footer' ) as $kind ) {
		$id = $values[ $kind ] ?? null;
		if ( '' === $id ) {
			delete_post_meta( $post_id, '_radman_' . $kind . '_layout' );
		} elseif ( 'none' === $id || ( \RadmanCT\Builder\Storage\valid_id( $id ) && \RadmanCT\Builder\Storage\exists( $kind, $id ) ) ) {
			update_post_meta( $post_id, '_radman_' . $kind . '_layout', $id );
		}
	}
}

add_action( 'add_meta_boxes_page', __NAMESPACE__ . '\\register' );
add_action( 'save_post_page', __NAMESPACE__ . '\\save' );
