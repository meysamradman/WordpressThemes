<?php

namespace RadmanCT\Admin\BlogMedia;

defined( 'ABSPATH' ) || exit;

function field( int $term_id = 0 ): void {
	$value = $term_id ? \RadmanCT\BlogMedia\sanitize( get_term_meta( $term_id, \RadmanCT\BlogMedia\META_KEY, true ) ) : '';
	wp_nonce_field( 'rdct_blog_media_category', 'rdct_blog_media_nonce' );
	echo '<select id="rdct-blog-media-type" name="rdct_blog_media_type" aria-describedby="rdct-blog-media-description">';
	foreach ( \RadmanCT\BlogMedia\labels() as $key => $label ) { echo '<option value="' . esc_attr( $key ) . '"' . selected( $value, $key, false ) . '>' . esc_html( $label ) . '</option>'; }
	echo '</select><p class="description" id="rdct-blog-media-description">' . esc_html__( 'Shows a music or video badge on blog images. Child categories inherit this choice unless overridden. Add the actual audio or video in the post editor.', 'radmanct' ) . '</p>';
}

function add_field(): void {
	echo '<div class="form-field"><label for="rdct-blog-media-type">' . esc_html__( 'Blog media badge', 'radmanct' ) . '</label>';
	field(); echo '</div>';
}

function edit_field( \WP_Term $term ): void {
	echo '<tr class="form-field"><th scope="row"><label for="rdct-blog-media-type">' . esc_html__( 'Blog media badge', 'radmanct' ) . '</label></th><td>';
	field( $term->term_id ); echo '</td></tr>';
}

function save( int $term_id ): void {
	$nonce = $_POST['rdct_blog_media_nonce'] ?? null;
	$value = $_POST['rdct_blog_media_type'] ?? null;
	if ( ! is_string( $nonce ) || ! is_string( $value ) || ! wp_verify_nonce( wp_unslash( $nonce ), 'rdct_blog_media_category' ) || ! current_user_can( 'edit_term', $term_id ) ) { return; }
	$value = wp_unslash( $value );
	if ( ! array_key_exists( $value, \RadmanCT\BlogMedia\labels() ) ) { return; }
	$term = get_term( $term_id, 'category' );
	if ( ! $term instanceof \WP_Term ) { return; }
	if ( '' === $value ) { delete_term_meta( $term_id, \RadmanCT\BlogMedia\META_KEY ); }
	else { update_term_meta( $term_id, \RadmanCT\BlogMedia\META_KEY, $value ); }
}

add_action( 'category_add_form_fields', __NAMESPACE__ . '\\add_field' );
add_action( 'category_edit_form_fields', __NAMESPACE__ . '\\edit_field' );
add_action( 'created_category', __NAMESPACE__ . '\\save' );
add_action( 'edited_category', __NAMESPACE__ . '\\save' );
