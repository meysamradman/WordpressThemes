<?php
namespace RadmanCT\Admin\PageTitle;
defined( 'ABSPATH' ) || exit;
function types(): array { return array_values( array_filter( array( 'page', 'post', 'radman_product', 'radman_portfolio' ), 'post_type_exists' ) ); }
function register(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) { return; }
	foreach ( types() as $type ) { add_meta_box( 'rdct-page-title', __( 'Page title', 'radmanct' ), __NAMESPACE__ . '\\render', $type, 'normal', 'default', array( '__block_editor_compatible_meta_box' => true ) ); }
}
function render( \WP_Post $post ): void {
	$value = \RadmanCT\PageTitle\normalize( get_post_meta( $post->ID, \RadmanCT\PageTitle\META, true ) ) ?? \RadmanCT\PageTitle\options();
	wp_nonce_field( 'rdct_page_title_' . $post->ID, 'rdct_page_title_nonce' );
	echo '<input type="hidden" id="rdct-page-title-value" name="rdct_page_title" value="' . esc_attr( wp_json_encode( $value ) ) . '"><div id="rdct-page-title-editor" class="rdct-cnt-editor" data-value="' . esc_attr( wp_json_encode( $value ) ) . '" data-labels="' . esc_attr( wp_json_encode( ( \RadmanCT\PageTitle\labels() + array( 'inheritAppearance' => __( 'Inherit / default', 'radmanct' ) ) ) ) ) . '"></div>';
}
function save( int $id ): void {
	$nonce = $_POST['rdct_page_title_nonce'] ?? null; $input = $_POST['rdct_page_title'] ?? null;
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $id ) || wp_is_post_autosave( $id ) || ! in_array( get_post_type( $id ), types(), true ) || ! current_user_can( 'edit_post', $id ) || ! current_user_can( 'edit_theme_options' ) || ! is_string( $nonce ) || ! wp_verify_nonce( wp_unslash( $nonce ), 'rdct_page_title_' . $id ) || ! is_string( $input ) || strlen( $input ) > 8192 ) { return; }
	$value = \RadmanCT\PageTitle\normalize( json_decode( wp_unslash( $input ), true ) );
	if ( null !== $value ) {
		if ( $value === \RadmanCT\PageTitle\normalize( \RadmanCT\PageTitle\options() ) ) { delete_post_meta( $id, \RadmanCT\PageTitle\META ); }
		else { update_post_meta( $id, \RadmanCT\PageTitle\META, $value ); }
	}
}
function assets( string $hook ): void {
	$screen = get_current_screen();
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || ! in_array( $screen->post_type, types(), true ) || ! current_user_can( 'edit_theme_options' ) ) { return; }
	wp_enqueue_media(); wp_enqueue_script( 'wp-element' );
	\RadmanCT\Assets\style( 'rdct-admin-title', 'src/css/admin/page-title.css', 'parent', array() );
	$asset = \RadmanCT\Assets\entry( 'src/js/admin/page-title-editor.js' );
	if ( $asset ) { wp_enqueue_script_module( 'rdct-page-title-editor', \RadmanCT\Assets\url( $asset['file'] ), array(), null ); }
}
add_action( 'add_meta_boxes', __NAMESPACE__ . '\\register' );
add_action( 'save_post', __NAMESPACE__ . '\\save' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\assets' );
