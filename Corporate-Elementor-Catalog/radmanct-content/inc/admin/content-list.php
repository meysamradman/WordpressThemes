<?php

namespace RadmanCTContent\Admin\ContentList;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/assets.php';

function columns( array $columns, bool $catalog = false ): array {
	$result = array();
	foreach ( $columns as $key => $label ) {
		$result[ $key ] = $label;
		if ( 'cb' === $key ) { $result['rdct_image'] = __( 'Image', 'radmanct-content' ); }
		if ( 'title' === $key ) {
			if ( $catalog ) { $result['rdct_code'] = __( 'Reference code', 'radmanct-content' ); }
			if ( $catalog ) { $result['rdct_pricing'] = __( 'Product price display', 'radmanct-content' ); }
		}
	}
	return $result;
}

function cell( string $column, int $post_id ): void {
	if ( 'rdct_image' === $column ) {
		echo get_the_post_thumbnail( $post_id, array( 56, 56 ), array( 'alt' => '', 'loading' => 'lazy' ) ) ?: '&mdash;';
	} elseif ( 'rdct_code' === $column ) {
		$data = \RadmanCTContent\Model\read( $post_id );
		echo '' !== $data['code'] ? '<bdi>' . esc_html( $data['code'] ) . '</bdi>' : '&mdash;';
	}
}

function enqueue( string $hook ): void {
	$screen = get_current_screen();
	if ( 'edit.php' === $hook && $screen && in_array( $screen->post_type, \RadmanCTContent\Modules\types(), true ) ) {
		\RadmanCTContent\Admin\Assets\style( 'rdct-content-list', 'src/css/admin/content-list.css' );
	}
}

if ( \RadmanCTContent\Modules\enabled( 'portfolio' ) ) {
	add_filter( 'manage_radman_portfolio_posts_columns', __NAMESPACE__ . '\\columns' );
	add_action( 'manage_radman_portfolio_posts_custom_column', __NAMESPACE__ . '\\cell', 10, 2 );
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue' );
