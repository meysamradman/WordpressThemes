<?php
defined( 'ABSPATH' ) || exit;
function rdct_title_expect( bool $ok, string $message ): void { if ( ! $ok ) { throw new RuntimeException( $message ); } }

$rdct_title_settings = get_option( 'radman_settings', array() );
$rdct_title_settings['page_title'] = \RadmanCT\PageTitle\defaults()['page_title'];
$rdct_title_filter = static function ( $value, $option ) use ( &$rdct_title_settings ) { return 'radman_settings' === $option ? $rdct_title_settings : $value; };
add_filter( 'pre_option', $rdct_title_filter, 10, 2 );
global $wp_query, $wp_the_query, $post;
$rdct_title_saved = array( $wp_query, $wp_the_query, $post, $_GET );
$rdct_title_meta = null; $rdct_title_meta_id = 0;
$rdct_title_meta_filter = static function ( $value, $id, $key ) use ( &$rdct_title_meta, &$rdct_title_meta_id ) {
	return \RadmanCT\PageTitle\META === $key && $id === $rdct_title_meta_id && null !== $rdct_title_meta ? array( $rdct_title_meta ) : $value;
};
add_filter( 'get_post_metadata', $rdct_title_meta_filter, 10, 3 );
$rdct_title_query = static function ( array $args ): void { global $wp_query, $wp_the_query, $post; $wp_query = new WP_Query(); $wp_the_query = $wp_query; $wp_query->query( $args ); $post = $wp_query->posts[0] ?? null; };
try {
	$rdct_curved = \RadmanCT\PageTitle\normalize( array( 'shape' => 'notched' ) );
	rdct_title_expect( 'notched' === $rdct_curved['shape'] && null === \RadmanCT\PageTitle\normalize( array( 'shape' => 'invalid' ) ), 'Invalid curved title schema' );
	$rdct_curved_requirements = \RadmanCTComponents\Components\requirements( 'page-title', $rdct_curved );
	rdct_title_expect( array( 'page-title', 'page-title-notched' ) === $rdct_curved_requirements['styles'] && ! $rdct_curved_requirements['script'], 'Curved title must select its CSS without runtime JS' );
	$rdct_plain_requirements = \RadmanCTComponents\Components\requirements( 'page-title' );
	rdct_title_expect( array( 'page-title' ) === $rdct_plain_requirements['styles'], 'Plain title loaded curved CSS' );
	foreach ( array( array( 'height' => 901 ), array( 'overlay' => '#fff;display:none' ), array( 'mode' => 'anything' ), array( 'background' => array( 'color' => 'url(x)' ) ), array( 'background' => array( 'image' => -1 ) ), array( 'background' => array( 'start' => 90, 'end' => 20 ) ) ) as $bad ) {
		rdct_title_expect( null === \RadmanCT\PageTitle\normalize( $bad ), 'Malformed title data accepted' );
	}
	$rdct_title_query( array( 'post_type' => 'radman_portfolio' ) );
	rdct_title_expect( ! \RadmanCT\PageTitle\active(), 'Default must preserve the existing archive' );
	\RadmanCT\PageTitle\assets(); rdct_title_expect( ! wp_style_is( 'rdct-page-title', 'enqueued' ), 'Disabled title loaded CSS' );
	$gradient = array( 'type' => 'gradient', 'color' => '#102030', 'second' => '#405060', 'angle' => 135 );
	foreach ( array( 'post' => 'blog', 'radman_product' => 'catalog', 'radman_portfolio' => 'portfolio' ) as $type => $scope ) {
		foreach ( array( 'archive', 'single' ) as $view ) {
			foreach ( \RadmanCT\PageTitle\scopes() as $key ) { $rdct_title_settings[ $key . '_page_title' ] = array( 'mode' => 'hide' ); }
			$key = $scope . '_' . $view;
			$rdct_title_settings[ $key . '_page_title' ] = array( 'mode' => 'show', 'custom' => true, 'background' => $gradient );
			if ( 'archive' === $view ) { $rdct_title_query( 'post' === $type ? array( 'post_type' => 'post' ) : array( 'post_type' => $type ) ); }
			else {
				$entries = get_posts( array( 'post_type' => $type, 'numberposts' => 1, 'post_status' => 'publish' ) );
				rdct_title_expect( (bool) $entries, 'Published local fixture missing' );
				$rdct_title_query( array( 'post_type' => $type, 'p' => $entries[0]->ID ) );
			}
			rdct_title_expect( \RadmanCT\PageTitle\active(), 'Independent scope not enabled: ' . $key );
			ob_start(); \RadmanCT\PageTitle\render(); $title_html = ob_get_clean();
			rdct_title_expect( 1 === substr_count( $title_html, '<h1 ' ) && str_contains( $title_html, 'linear-gradient(135deg,#102030 0%,#405060 100%)' ), 'Title renderer failed: ' . $key );
			rdct_title_expect( str_contains( $title_html, 'aria-current="page"' ), 'Accessible breadcrumb missing' );
			ob_start(); \RadmanCT\PageTitle\render();
			if ( 'single' === $view ) { get_template_part( 'template-parts/' . ( 'catalog' === $scope ? 'catalog' : ( 'portfolio' === $scope ? 'portfolio' : 'blog' ) ) . '/single' ); }
			else { get_template_part( 'template-parts/' . ( 'catalog' === $scope ? 'catalog' : ( 'portfolio' === $scope ? 'portfolio' : 'blog' ) ) . '/archive' ); }
			$document = ob_get_clean();
			rdct_title_expect( 1 === preg_match_all( '/<h1\b/', $document ), 'Duplicate or missing H1: ' . $key );
		}
	}
	$rdct_title_settings['portfolio_archive_page_title']['mode'] = 'show';
	$rdct_title_query( array( 'post_type' => 'radman_portfolio' ) );
	ob_start(); \RadmanCT\PageTitle\render(); get_template_part( 'template-parts/portfolio/archive' ); $full_html = ob_get_clean();
	rdct_title_expect( 1 === preg_match_all( '/<h1\b/', $full_html ), 'Duplicate native H1 in portfolio archive' );
	$_GET['_rdct_fragment'] = 'portfolio';
	ob_start(); \RadmanCT\PageTitle\render(); $fragment = ob_get_clean();
	rdct_title_expect( str_contains( $fragment, 'rdct-page-title' ), 'Fragment omitted title or requirements' );
	unset( $_GET['_rdct_fragment'] );
	$pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 1 ) );
	$rdct_title_meta_id = $pages[0]->ID;
	$rdct_title_query( array( 'page_id' => $rdct_title_meta_id ) );
	$rdct_title_meta = array( 'mode' => 'show', 'custom' => true, 'background' => array( 'type' => 'color', 'color' => '#abcdef' ) );
	rdct_title_expect( '#abcdef' === \RadmanCT\PageTitle\current()['background']['color'], 'Per-page background override failed' );
	$rdct_title_meta['mode'] = 'hide'; rdct_title_expect( ! \RadmanCT\PageTitle\active(), 'Per-page hide failed' );
	$rdct_title_meta['mode'] = 'inherit'; rdct_title_expect( ! \RadmanCT\PageTitle\active(), 'Global hide inheritance failed' );
	$rdct_title_settings['page_title']['mode'] = 'show';
	$rdct_title_meta['mode'] = 'show';
	$rdct_title_settings['page_title_typography'] = array();
	\RadmanCT\PageTitle\assets(); rdct_title_expect( ! wp_style_is( 'rdct-title-typography', 'enqueued' ), 'Empty typography duplicated CSS' );
	$rdct_title_settings['page_title_typography'] = array( 'h1_color' => '#aabbcc' );
	\RadmanCT\PageTitle\assets();
	$css = implode( '', wp_styles()->get_data( 'rdct-title-typography', 'after' ) ?: array() );
	rdct_title_expect( str_contains( $css, '--rdct-text-h1-color:#aabbcc' ) && ! str_contains( $css, '@font-face' ), 'Color-only title typography duplicated font faces' );
	$row = \RadmanCT\Builder\appearance( array( 'backgroundOptions' => $gradient ), true );
	rdct_title_expect( str_contains( \RadmanCT\Builder\appearance_attributes( $row, true ), 'linear-gradient(' ), 'Builder gradient adapter missing' );
	rdct_title_expect( str_contains( \RadmanCT\ContentColors\css( array( 'gradient' => $gradient ) ), '#main-content{background:linear-gradient(' ), 'Content gradient escaped its owner' );
	$images = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_mime_type' => 'image', 'numberposts' => 1 ) );
	rdct_title_expect( (bool) $images, 'Image fixture missing' );
	$image_options = \RadmanCT\PageTitle\options(); $image_options['background'] = array( 'type' => 'image', 'image' => $images[0]->ID );
	$image_html = \RadmanCTComponents\PageTitle\render( '<script>title</script>', array(), $image_options );
	rdct_title_expect( str_contains( $image_html, '<img ' ) && ! str_contains( $image_html, '<script>' ) && ! str_contains( $image_html, '<video' ), 'Image mode or escaping failed' );
	$clip = '/tmp/radman-preview-test.webm';
	rdct_title_expect( is_readable( $clip ), 'Local procedural test clip missing' );
	$uploaded = wp_upload_bits( 'rdct-page-title-test.webm', null, file_get_contents( $clip ) );
	rdct_title_expect( ! $uploaded['error'], 'Temporary clip upload failed' );
	$video_id = wp_insert_attachment( array( 'post_title' => 'Radman title test fixture', 'post_mime_type' => 'video/webm', 'post_status' => 'inherit' ), $uploaded['file'] );
	try {
		rdct_title_expect( $video_id > 0, 'Temporary video attachment failed' );
		$image_options['background'] = array( 'type' => 'video', 'image' => $images[0]->ID, 'video' => $video_id );
		$video_html = \RadmanCTComponents\PageTitle\render( 'Test', array(), $image_options );
		rdct_title_expect( str_contains( $video_html, 'data-src=' ) && str_contains( $video_html, 'muted loop playsinline preload="none"' ) && str_contains( $video_html, 'data-rdct-preview-control' ) && ! preg_match( '/<video[^>]+\ssrc=/', $video_html ), 'Background video must use the shared lazy, silent, pausable engine' );
		$rdct_title_meta = array( 'mode' => 'show', 'custom' => true, 'background' => $image_options['background'] );
		$post = clone get_post( $rdct_title_meta_id ); $post->post_password = 'test-only';
		ob_start(); \RadmanCT\PageTitle\render(); $protected_html = ob_get_clean();
		rdct_title_expect( ! str_contains( $protected_html, '<video' ) && ! str_contains( $protected_html, '<img' ), 'Protected page exposed title media' );
		$post = get_post( $rdct_title_meta_id );
	} finally { if ( $video_id > 0 ) { wp_delete_attachment( $video_id, true ); } else { wp_delete_file( $uploaded['file'] ); } }
	require_once get_template_directory() . '/inc/admin/page-title.php';
	$old_user = get_current_user_id(); $old_post_data = $_POST; $writes = array();
	$capture = static function ( $check, $id, $key, $value ) use ( &$writes ) { if ( \RadmanCT\PageTitle\META === $key ) { $writes[] = $value; return true; } return $check; };
	add_filter( 'update_post_metadata', $capture, 10, 4 );
	try {
		$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) ); wp_set_current_user( (int) $admins[0] );
		$_POST = array( 'rdct_page_title_nonce' => 'bad', 'rdct_page_title' => wp_slash( wp_json_encode( array( 'mode' => 'show' ) ) ) );
		\RadmanCT\Admin\PageTitle\save( $rdct_title_meta_id ); rdct_title_expect( ! $writes, 'Invalid nonce wrote metadata' );
		$_POST['rdct_page_title_nonce'] = wp_create_nonce( 'rdct_page_title_' . $rdct_title_meta_id );
		\RadmanCT\Admin\PageTitle\save( $rdct_title_meta_id ); rdct_title_expect( 1 === count( $writes ) && 'show' === $writes[0]['mode'], 'Authorized native save failed' );
		$_POST['rdct_page_title'] = '{bad json'; \RadmanCT\Admin\PageTitle\save( $rdct_title_meta_id ); rdct_title_expect( 1 === count( $writes ), 'Malformed JSON overwrote existing metadata' );
		wp_set_current_user( 0 ); $_POST['rdct_page_title_nonce'] = wp_create_nonce( 'rdct_page_title_' . $rdct_title_meta_id ); $_POST['rdct_page_title'] = wp_slash( wp_json_encode( array( 'mode' => 'show' ) ) );
		\RadmanCT\Admin\PageTitle\save( $rdct_title_meta_id ); rdct_title_expect( 1 === count( $writes ), 'Guest wrote title metadata' );
	} finally { remove_filter( 'update_post_metadata', $capture, 10 ); wp_set_current_user( $old_user ); $_POST = $old_post_data; }
	echo "Page title: validation, six independent views, per-page overrides, one H1, breadcrumbs, fragments, typography deltas and shared gradients passed\n";
	echo "Page title: image/video fallback, local lazy player, protected media and native save authorization passed\n";
} finally {
	remove_filter( 'pre_option', $rdct_title_filter, 10 ); remove_filter( 'get_post_metadata', $rdct_title_meta_filter, 10 );
	[ $wp_query, $wp_the_query, $post, $_GET ] = $rdct_title_saved;
}
