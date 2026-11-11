<?php

namespace RadmanCT\Admin\PageEffects;

defined( 'ABSPATH' ) || exit;

function register(): void {
	if ( current_user_can( 'edit_theme_options' ) ) {
		add_meta_box( 'rdct-page-effects', __( 'Radman CT page effects', 'radmanct' ), __NAMESPACE__ . '\\render', 'page', 'side' );
	}
}

function render( \WP_Post $post ): void {
	wp_nonce_field( 'radman_page_effects', 'radman_effects_nonce' );
	$values = \RadmanCT\PageEffects\normalize( get_post_meta( $post->ID, '_radman_page_effects', true ) );
	$labels = array(
		'scroll_reveal' => __( 'Scroll reveal animations', 'radmanct' ),
		'smooth_scroll' => __( 'Smooth scrolling', 'radmanct' ),
		'custom_cursor' => __( 'Circular mouse cursor', 'radmanct' ),
		'scroll_progress' => __( 'Top scroll progress bar', 'radmanct' ),
		'preloader' => __( 'Enable preloader', 'radmanct' ),
	);
	$choices = array( '' => __( 'Follow theme settings', 'radmanct' ), 'on' => __( 'Enable on this page', 'radmanct' ), 'off' => __( 'Disable on this page', 'radmanct' ) );
	echo '<p>' . esc_html__( 'Override effects for this page only. Editor previews always keep effects disabled.', 'radmanct' ) . '</p>';
	foreach ( $labels as $key => $label ) {
		echo '<p><label for="rdct-effect-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label><select class="widefat" id="rdct-effect-' . esc_attr( $key ) . '" name="radman_page_effects[' . esc_attr( $key ) . ']">';
		foreach ( $choices as $value => $title ) {
			echo '<option value="' . esc_attr( $value ) . '"' . selected( $values[ $key ] ?? '', $value, false ) . '>' . esc_html( $title ) . '</option>';
		}
		echo '</select></p>';
	}
}

function save( int $post_id ): void {
	$nonce = $_POST['radman_effects_nonce'] ?? null;
	$input = $_POST['radman_page_effects'] ?? null;
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || ! is_string( $nonce ) || ! wp_verify_nonce( $nonce, 'radman_page_effects' ) || ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'edit_post', $post_id ) || ! is_array( $input ) ) {
		return;
	}
	$values = \RadmanCT\PageEffects\normalize( get_post_meta( $post_id, '_radman_page_effects', true ) );
	foreach ( \RadmanCT\PageEffects\KEYS as $key ) {
		$value = $input[ $key ] ?? null;
		if ( '' === $value ) {
			unset( $values[ $key ] );
		} elseif ( in_array( $value, array( 'on', 'off' ), true ) ) {
			$values[ $key ] = $value;
		}
	}
	if ( $values ) {
		update_post_meta( $post_id, '_radman_page_effects', $values );
	} else {
		delete_post_meta( $post_id, '_radman_page_effects' );
	}
}

add_action( 'add_meta_boxes_page', __NAMESPACE__ . '\\register' );
add_action( 'save_post_page', __NAMESPACE__ . '\\save' );
