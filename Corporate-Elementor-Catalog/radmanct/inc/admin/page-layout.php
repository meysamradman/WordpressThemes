<?php

namespace RadmanCT\Admin\PageLayout;

defined( 'ABSPATH' ) || exit;

function register(): void {
	if ( current_user_can( 'edit_theme_options' ) ) {
		add_meta_box( 'rdct-page-layout', __( 'Radman CT content layout', 'radmanct' ), __NAMESPACE__ . '\\render', 'page', 'normal', 'default', array( '__block_editor_compatible_meta_box' => true ) );
	}
}

function number_field( string $key, array $range, array $values, array $labels ): void {
	$id = 'rdct-page-layout-' . $key;
	echo '<p><label for="' . esc_attr( $id ) . '">' . esc_html( $labels[ 'layout_' . $key ] ) . '</label> ';
	echo '<input class="small-text" type="number" dir="ltr" id="' . esc_attr( $id ) . '" name="rdct_page_layout[' . esc_attr( $key ) . ']" min="' . (int) $range[0] . '" max="' . (int) $range[1] . '" step="1" placeholder="' . esc_attr( $labels['inheritAppearance'] ) . '" value="' . esc_attr( (string) ( $values[ $key ] ?? '' ) ) . '"></p>';
}

function render( \WP_Post $post ): void {
	wp_nonce_field( 'rdct_page_layout_' . $post->ID, 'rdct_page_layout_nonce' );
	$values = \RadmanCT\Layout\Page\normalize( get_post_meta( $post->ID, \RadmanCT\Layout\Page\META_KEY, true ) );
	$labels = \RadmanCT\Admin\SettingsPage\labels();
	echo '<p>' . esc_html__( 'Applies only to Radman-rendered page content. Elementor controls its own layout; header and footer settings stay independent.', 'radmanct' ) . '</p>';
	echo '<p><label for="rdct-page-layout-mode">' . esc_html( $labels['layoutMode'] ) . '</label> <select id="rdct-page-layout-mode" name="rdct_page_layout[layout]">';
	foreach ( array( 'inherit' => $labels['inheritLayout'], 'contained' => $labels['siteLayoutWidth'], 'full' => $labels['fullContentWidth'], 'custom' => $labels['customLayoutWidth'] ) as $key => $title ) {
		echo '<option value="' . esc_attr( $key ) . '"' . selected( $values['layout'] ?? 'inherit', $key, false ) . '>' . esc_html( $title ) . '</option>';
	}
	echo '</select></p><p class="description">' . esc_html__( 'Global mode ignores the overrides below. Custom maximum width is used only in custom mode. Empty spacing inherits; zero removes spacing.', 'radmanct' ) . '</p>';
	$fields = \RadmanCT\Layout\fields();
	number_field( 'width', $fields['width'], $values, $labels );
	foreach ( array( '' => __( 'Desktop', 'radmanct' ), 'tablet_' => __( 'Tablet', 'radmanct' ), 'mobile_' => __( 'Mobile', 'radmanct' ) ) as $prefix => $title ) {
		echo '<details><summary>' . esc_html( $title ) . '</summary>';
		foreach ( array( 'gutter', 'top', 'bottom' ) as $field ) { $key = $prefix . $field; number_field( $key, $fields[ $key ], $values, $labels ); }
		echo '</details>';
	}
}

function save( int $post_id ): void {
	$nonce = $_POST['rdct_page_layout_nonce'] ?? null;
	$input = $_POST['rdct_page_layout'] ?? null;
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || ! is_string( $nonce ) || ! wp_verify_nonce( $nonce, 'rdct_page_layout_' . $post_id ) || ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'edit_post', $post_id ) || ! is_array( $input ) || 'page' !== get_post_type( $post_id ) ) { return; }
	$values = \RadmanCT\Layout\Page\normalize( wp_unslash( $input ), get_post_meta( $post_id, \RadmanCT\Layout\Page\META_KEY, true ) );
	if ( $values ) { update_post_meta( $post_id, \RadmanCT\Layout\Page\META_KEY, $values ); } else { delete_post_meta( $post_id, \RadmanCT\Layout\Page\META_KEY ); }
}

add_action( 'add_meta_boxes_page', __NAMESPACE__ . '\\register' );
add_action( 'save_post_page', __NAMESPACE__ . '\\save' );
