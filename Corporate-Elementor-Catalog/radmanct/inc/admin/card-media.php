<?php

namespace RadmanCT\Admin\CardMedia;

defined( 'ABSPATH' ) || exit;

function register(): void {
	if ( ! function_exists( '\\RadmanCTComponents\\Components\\CardPreview\\video' ) ) { return; }
	foreach ( \RadmanCT\CardMedia\types() as $type ) { add_meta_box( 'rdct-card-media', __( 'Card video preview', 'radmanct' ), __NAMESPACE__ . '\\render', $type, 'side', 'default', array( '__block_editor_compatible_meta_box' => true ) ); }
}

function render( \WP_Post $post ): void {
	$id = \RadmanCT\CardMedia\valid_id( get_post_meta( $post->ID, \RadmanCT\CardMedia\VIDEO_KEY, true ) );
	$mode = \RadmanCT\CardMedia\mode( get_post_meta( $post->ID, \RadmanCT\CardMedia\MODE_KEY, true ) );
	wp_nonce_field( 'rdct_card_media_' . $post->ID, 'rdct_card_media_nonce' );
	echo '<div data-rdct-card-media-editor data-title="' . esc_attr__( 'Choose a video preview', 'radmanct' ) . '" data-error="' . esc_attr__( 'Use an MP4, WebM or Ogg video.', 'radmanct' ) . '"><input type="hidden" name="rdct_card_video_id" value="' . (int) $id . '"><p><span data-video-name>' . esc_html( $id ? basename( get_attached_file( $id ) ) : __( 'No video selected', 'radmanct' ) ) . '</span></p><p><button class="button" type="button" data-video-select>' . esc_html__( 'Choose video', 'radmanct' ) . '</button> <button class="button-link-delete" type="button" data-video-remove' . ( $id ? '' : ' hidden' ) . '>' . esc_html__( 'Remove', 'radmanct' ) . '</button></p><p><label for="rdct-card-video-mode">' . esc_html__( 'Preview playback', 'radmanct' ) . '</label><br><select id="rdct-card-video-mode" name="rdct_card_video_mode">';
	foreach ( array( 'auto' => __( 'While visible', 'radmanct' ), 'hover' => __( 'On hover or keyboard focus', 'radmanct' ) ) as $key => $label ) { echo '<option value="' . esc_attr( $key ) . '"' . selected( $mode, $key, false ) . '>' . esc_html( $label ) . '</option>'; }
	echo '</select></p><p class="description">' . esc_html__( 'The featured image remains the poster. Card previews are silent and do not replace the video inside your content.', 'radmanct' ) . '</p><p data-video-status role="status" aria-live="polite"></p></div>';
}

function save( int $post_id ): void {
	$nonce = $_POST['rdct_card_media_nonce'] ?? null;
	$input = $_POST['rdct_card_video_id'] ?? null;
	$mode = $_POST['rdct_card_video_mode'] ?? null;
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || ! in_array( get_post_type( $post_id ), \RadmanCT\CardMedia\types(), true ) || ! is_string( $nonce ) || ! wp_verify_nonce( wp_unslash( $nonce ), 'rdct_card_media_' . $post_id ) || ! current_user_can( 'edit_post', $post_id ) || ! is_string( $input ) || ! is_string( $mode ) || ! in_array( $mode, array( 'auto', 'hover' ), true ) ) { return; }
	$value = filter_var( wp_unslash( $input ), FILTER_VALIDATE_INT );
	if ( false === $value || $value < 0 || ( $value && ! \RadmanCT\CardMedia\valid_id( $value ) ) ) { return; }
	if ( ! $value ) { delete_post_meta( $post_id, \RadmanCT\CardMedia\VIDEO_KEY ); delete_post_meta( $post_id, \RadmanCT\CardMedia\MODE_KEY ); return; }
	update_post_meta( $post_id, \RadmanCT\CardMedia\VIDEO_KEY, $value );
	update_post_meta( $post_id, \RadmanCT\CardMedia\MODE_KEY, $mode );
}

function assets( string $hook ): void {
	$screen = get_current_screen();
	if ( ! function_exists( '\\RadmanCTComponents\\Components\\CardPreview\\video' ) || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || ! in_array( $screen->post_type, \RadmanCT\CardMedia\types(), true ) ) { return; }
	wp_enqueue_media();
	$entry = \RadmanCT\Assets\entry( 'src/js/admin/card-media.js' );
	if ( $entry ) { wp_enqueue_script_module( 'rdct-card-media-admin', \RadmanCT\Assets\url( $entry['file'] ), array(), null ); }
}

add_action( 'add_meta_boxes', __NAMESPACE__ . '\\register' );
add_action( 'save_post', __NAMESPACE__ . '\\save' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\assets' );
