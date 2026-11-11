<?php

namespace RadmanCTContent\Admin\Content;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/assets.php';

function labels(): array {
	return array(
		'productDisplay' => __( 'Product price display', 'radmanct-content' ), 'contactUs' => __( 'Contact us', 'radmanct-content' ), 'price' => __( 'Price', 'radmanct-content' ),
		'priceHint' => __( 'Enter the price and currency as display text. This does not enable online purchasing.', 'radmanct-content' ),
		'contactHint' => __( 'Uses the contact page selected in Core settings. No form or request submission is created.', 'radmanct-content' ),
		'contactMissing' => __( 'No public contact page is available for this language. The contact button will stay hidden until a published page is selected in Core settings.', 'radmanct-content' ),
		'contactSettings' => __( 'Choose a contact page in Core settings', 'radmanct-content' ),
		'productError' => __( 'The product details editor could not load. Saved values were preserved.', 'radmanct-content' ),
		'code' => __( 'Reference code', 'radmanct-content' ), 'gallery' => __( 'Image gallery', 'radmanct-content' ), 'addImages' => __( 'Choose images', 'radmanct-content' ), 'remove' => __( 'Remove', 'radmanct-content' ),
		'up' => __( 'Move up', 'radmanct-content' ), 'down' => __( 'Move down', 'radmanct-content' ), 'specifications' => __( 'Technical specifications', 'radmanct-content' ),
		'addSpec' => __( 'Add specification', 'radmanct-content' ), 'label' => __( 'Label', 'radmanct-content' ), 'value' => __( 'Value', 'radmanct-content' ), 'unit' => __( 'Unit', 'radmanct-content' ),
		'limit' => __( 'Up to 20 images and 40 specifications.', 'radmanct-content' ), 'empty' => __( 'No images selected.', 'radmanct-content' ),
		'galleryLimit' => __( 'Up to 20 images.', 'radmanct-content' ),
		'specError' => __( 'The specifications editor could not load. Saved values were preserved.', 'radmanct-content' ),
	);
}

function render( \WP_Post $post ): void {
	wp_nonce_field( 'radman_content_' . $post->ID, 'radman_content_nonce' );
	echo '<div id="rdct-content-root"></div><noscript>' . esc_html__( 'Enable JavaScript to edit these details. Existing values will be preserved.', 'radmanct-content' ) . '</noscript>';
}

function enqueue( string $hook ): void {
	if ( 'elementor' === ( $_GET['action'] ?? null ) || did_action( 'elementor/editor/init' ) ) { return; }
	$screen = get_current_screen();
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || ! in_array( $screen->post_type, \RadmanCTContent\Modules\types(), true ) ) {
		return;
	}
	global $post;
	if ( ! $post || ! current_user_can( 'edit_post', $post->ID ) ) {
		return;
	}
	if ( ! \RadmanCTContent\Admin\Assets\entry( 'src/js/admin/content.js' ) || ! \RadmanCTContent\Admin\Assets\entry( 'src/css/admin/content.css' ) ) {
		return;
	}
	wp_enqueue_media();
	\RadmanCTContent\Admin\Assets\style( 'radmanct-content-content', 'src/css/admin/content.css' );
	\RadmanCTContent\Admin\Assets\script( 'radmanct-content-content', 'src/js/admin/content.js', array( 'wp-element', 'media-views' ) );
	$data = \RadmanCTContent\Model\read( $post->ID );
	$images = array();
	if ( $data['gallery'] ) {
		get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post__in' => $data['gallery'], 'posts_per_page' => 20, 'no_found_rows' => true, 'update_post_meta_cache' => true, 'update_post_term_cache' => false ) );
	}
	foreach ( $data['gallery'] as $id ) {
		$images[ $id ] = wp_get_attachment_image_url( $id, 'thumbnail' ) ?: '';
	}
	$config = array( 'data' => $data, 'images' => $images, 'labels' => labels(), 'product' => 'radman_product' === $post->post_type );
	if ( $config['product'] ) {
		$config['contactAvailable'] = '' !== \RadmanCTContent\Catalog\contact_url();
		$config['contactSettings'] = current_user_can( 'manage_options' ) ? admin_url( 'admin.php?page=radmanct-content' ) : '';
	}
	wp_add_inline_script( 'radmanct-content-content', 'window.rdctContent=' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}

function save( int $post_id ): void {
	$nonce = $_POST['radman_content_nonce'] ?? null;
	$raw = $_POST['radman_content_data'] ?? null;
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! in_array( get_post_type( $post_id ), \RadmanCTContent\Modules\types(), true ) || ! is_string( $nonce ) || ! wp_verify_nonce( $nonce, 'radman_content_' . $post_id ) || ! current_user_can( 'edit_post', $post_id ) || ! is_string( $raw ) ) {
		return;
	}
	$data = strlen( $raw ) <= 131072 ? \RadmanCTContent\Model\validate( json_decode( wp_unslash( $raw ), true, 8 ) ) : null;
	if ( $data ) {
		foreach ( $data['gallery'] as $id ) {
			if ( ! wp_attachment_is_image( $id ) || ! current_user_can( 'read_post', $id ) ) {
				$data = null;
				break;
			}
		}
	}
	if ( ! $data ) {
		set_transient( 'radman_core_invalid_' . get_current_user_id(), true, 60 );
		return;
	}
	if ( 'radman_portfolio' === get_post_type( $post_id ) ) { $data['code'] = ''; }
	update_post_meta( $post_id, \RadmanCTContent\Model\META, wp_slash( $data ) );
}

foreach ( \RadmanCTContent\Modules\types() as $rdct_type ) {
	add_action( 'add_meta_boxes_' . $rdct_type, static function (): void { add_meta_box( 'rdct-content', __( 'Radman CT content details', 'radmanct-content' ), __NAMESPACE__ . '\\render', get_current_screen()->post_type, 'normal', 'high' ); } );
	add_action( 'save_post_' . $rdct_type, __NAMESPACE__ . '\\save' );
}
unset( $rdct_type );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue' );
add_filter( 'wp_script_attributes', static function ( array $attributes ): array { if ( 'radmanct-content-content-js' === ( $attributes['id'] ?? '' ) ) { $attributes['type'] = 'module'; } return $attributes; } );
add_action( 'admin_notices', static function (): void {
	$key = 'radman_core_invalid_' . get_current_user_id();
	if ( get_transient( $key ) ) {
		delete_transient( $key );
		echo '<div class="notice notice-error"><p>' . esc_html__( 'Content details were invalid. Previously saved details were preserved.', 'radmanct-content' ) . '</p></div>';
	}
} );
