<?php
namespace RadmanCT\PageTitle;
defined( 'ABSPATH' ) || exit;

function current(): array {
	if ( is_admin() || is_feed() || is_404() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || \RadmanCT\Renderers\uses_elementor() || ! function_exists( '\\RadmanCTComponents\\PageTitle\\render' ) ) { return array(); }
	$settings = \RadmanCT\Settings\values();
	$value = normalize( $settings['page_title'] ) ?? options();
	$context = \RadmanCT\Renderers\context();
	$scope = implode( '_', $context );
	if ( in_array( $scope, scopes(), true ) ) { $value = merge( $value, normalize( $settings[ $scope . '_page_title' ] ) ?? options() ); }
	elseif ( ! is_page() ) { return array(); }
	$id = is_home() ? (int) get_option( 'page_for_posts' ) : ( is_singular() ? get_queried_object_id() : 0 );
	$meta = $id ? normalize( get_post_meta( $id, META, true ) ) : null;
	if ( $meta ) { $value = merge( $value, $meta ); }
	if ( is_page() && \RadmanCT\Layout\elementor_content() && 'show' !== ( $meta['mode'] ?? '' ) ) { return array(); }
	return 'show' === $value['mode'] ? $value : array();
}

function active(): bool { return (bool) current(); }

function title(): string {
	if ( is_singular() ) { return get_the_title( get_queried_object_id() ); }
	if ( is_home() ) { return get_the_title( (int) get_option( 'page_for_posts' ) ) ?: __( 'Blog', 'radmanct' ); }
	if ( is_search() ) { return sprintf( __( 'Search results for: %s', 'radmanct' ), get_search_query( false ) ); }
	if ( is_tax() || is_category() || is_tag() ) { return single_term_title( '', false ); }
	if ( is_post_type_archive() ) { return post_type_archive_title( '', false ); }
	return wp_strip_all_tags( get_the_archive_title() );
}

function crumbs(): array {
	$result = array( array( 'label' => __( 'Home', 'radmanct' ), 'url' => home_url( '/' ) ) );
	if ( is_page() ) {
		foreach ( array_slice( array_reverse( get_post_ancestors( get_queried_object_id() ) ), -20 ) as $id ) {
			if ( 'publish' === get_post_status( $id ) && ! post_password_required( $id ) ) { $result[] = array( 'label' => get_the_title( $id ), 'url' => get_permalink( $id ) ); }
		}
	} elseif ( is_singular( array( 'radman_product', 'radman_portfolio' ) ) || is_tax() ) {
		$type = is_singular() ? get_post_type() : ( is_tax( 'radman_portfolio_cat' ) ? 'radman_portfolio' : 'radman_product' );
		$object = get_post_type_object( $type ); $url = get_post_type_archive_link( $type );
		if ( $object && $url ) { $result[] = array( 'label' => $object->labels->name, 'url' => $url ); }
	} elseif ( is_singular( 'post' ) || is_category() || is_tag() ) {
		$id = (int) get_option( 'page_for_posts' );
		if ( $id && 'publish' === get_post_status( $id ) ) { $result[] = array( 'label' => get_the_title( $id ), 'url' => get_permalink( $id ) ); }
	}
	$result[] = array( 'label' => title(), 'url' => '' );
	return $result;
}

function render(): void {
	$options = current();
	if ( ! $options ) { return; }
	if ( isset( $_GET['_rdct_fragment'] ) ) {
		$before = wp_styles()->queue; assets();
		wp_print_styles( array_unique( array_merge( array( 'rdct-page-title', 'rdct-title-typography' ), array_diff( wp_styles()->queue, $before ) ) ) );
	}
	if ( is_singular() && post_password_required() ) { $options['background'] = array( 'type' => 'color', 'color' => $options['background']['color'] ?? '' ); }
	$surface = \RadmanCT\ContentColors\current();
	$options['surface'] = $surface['gradient'] ?? array( 'type' => 'color', 'color' => $surface['background'] ?? '' );
	echo \RadmanCTComponents\PageTitle\render( title(), crumbs(), $options );
}

function assets(): void {
	$options = current();
	if ( ! $options ) { return; }
	\RadmanCTComponents\Components\enqueue( 'page-title', $options );
	do_action( 'radmanct/page_title/assets' );
	if ( ( ! is_singular() || ! post_password_required() ) && 'video' === ( $options['background']['type'] ?? '' ) && \RadmanCTComponents\Components\CardPreview\video( $options['background']['video'] ?? 0 ) ) { \RadmanCTComponents\Components\enqueue( 'card-preview' ); }
	$base = \RadmanCT\Settings\values(); $raw = $base['page_title_typography'];
	if ( ! $raw ) { return; }
	$baseline = $base;
	$settings = \RadmanCT\Typography\Profiles\resolve( $baseline, $raw );
	foreach ( \RadmanCT\Typography\Profiles\fields( $base ) as $key => $unused ) { if ( preg_match( '/_(?:size|line_height|color)$/D', $key ) && ! isset( $raw[ $key ] ) ) { $settings[ $key ] = ''; } }
	$fonts = \RadmanCT\Typography\used( \RadmanCT\Typography\selections( $settings ), $settings );
	$font_handles = \RadmanCT\Typography\enqueue_font_faces( $fonts );
	$css = \RadmanCT\Typography\css( $settings, 'body.rdct-site .rdct-page-title', false, $baseline );
	if ( $css && ! wp_style_is( 'rdct-title-typography', 'enqueued' ) ) { wp_register_style( 'rdct-title-typography', false, $font_handles, null ); wp_enqueue_style( 'rdct-title-typography' ); wp_add_inline_style( 'rdct-title-typography', $css ); }
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 35 );
