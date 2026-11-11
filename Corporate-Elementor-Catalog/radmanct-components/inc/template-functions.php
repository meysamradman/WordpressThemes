<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'rdct_use' ) ) {

function rdct_prepare( callable $render ): string {
	if ( did_action( 'wp_head' ) ) { throw new \LogicException( 'Prepare page content before get_header().' ); }
	$level = ob_get_level();
	ob_start();
	try {
		$render();
		while ( ob_get_level() > $level + 1 ) { ob_end_flush(); }
		return (string) ob_get_clean();
	} catch ( \Throwable $error ) {
		while ( ob_get_level() > $level ) { ob_end_clean(); }
		throw $error;
	}
}

function rdct_use( string $component ): void {
	if ( isset( \RadmanCTComponents\Components\definitions()[ $component ] ) ) { \RadmanCTComponents\Components\use_component( $component ); }
	elseif ( function_exists( '\\RadmanCT\\Components\\use_component' ) ) { \RadmanCT\Components\use_component( $component ); }
}

function rdct_taxonomy_list( array $options = array() ): void {
	require_once __DIR__ . '/components/taxonomy-list.php';
	$html = \RadmanCTComponents\Components\TaxonomyList\render( $options );
	if ( '' === $html ) { return; }
	\RadmanCTComponents\Components\enqueue( 'taxonomy-list', $options );
	echo $html;
}

function rdct_archive_search( array $options = array() ): void {
	require_once __DIR__ . '/components/archive-search.php';
	$html = \RadmanCTComponents\Components\ArchiveSearch\render( $options );
	if ( '' === $html ) { return; }
	\RadmanCTComponents\Components\enqueue( 'archive-search', $options );
	echo $html;
}

function rdct_curtain( array $items, array $options = array() ): void {
	require_once __DIR__ . '/components/curtain.php';
	$html = \RadmanCTComponents\Components\Curtain\render( $items, $options );
	if ( '' !== $html ) {
		\RadmanCTComponents\Components\enqueue( 'curtain', $options );
		echo $html;
	}
}

function rdct_button( array $options ): void {
	require_once __DIR__ . '/components/button.php';
	$html = \RadmanCTComponents\Components\Button\render( $options );
	if ( '' === $html ) { return; }
	\RadmanCTComponents\Components\enqueue( 'button', $options );
	echo $html;
}

function rdct_box( array $options ): void {
	require_once __DIR__ . '/components/box.php';
	$html = \RadmanCTComponents\Components\Box\render( $options );
	if ( '' === $html ) { return; }
	\RadmanCTComponents\Components\enqueue( 'box', $options );
	echo $html;
}

function rdct_icon( string $name, array $options = array() ): void {
	require_once __DIR__ . '/icons.php';
	$html = \RadmanCTComponents\Icons\render( $name, $options );
	if ( '' !== $html ) {
		\RadmanCTComponents\Components\enqueue( 'icons', $options );
		echo $html;
	}
}

function rdct_border_attributes( array $options = array() ): void {
	require_once __DIR__ . '/components/hover-border.php';
	echo \RadmanCTComponents\Components\HoverBorder\attributes( $options );
}

function rdct_text_reveal( string $text, array $options = array() ): void {
	require_once __DIR__ . '/components/text-reveal.php';
	$html = \RadmanCTComponents\Components\TextReveal\render( $text, $options );
	if ( '' !== $html ) {
		\RadmanCTComponents\Components\enqueue( 'text-reveal', $options );
		echo $html;
	}
}

function rdct_flipbox( array $options ): void {
 require_once __DIR__ . '/components/flipbox.php';
 \RadmanCTComponents\Components\enqueue( 'flipbox', $options );
 echo \RadmanCTComponents\Components\FlipBox\render( $options );
}

function rdct_icon_box( array $options ): void {
 require_once __DIR__ . '/components/icon-box.php';
 $html = \RadmanCTComponents\Components\IconBox\render( $options );
 if ( '' === $html ) { return; }
 \RadmanCTComponents\Components\enqueue( 'icon-box', $options );
 echo $html;
}

function rdct_image( array $options ): void {
 require_once __DIR__ . '/components/image.php';
 $html = \RadmanCTComponents\Components\Image\render( $options );
 if ( '' === $html ) { return; }
 \RadmanCTComponents\Components\enqueue( 'image', $options );
 echo $html;
}

function rdct_table( array $data, array $options = array() ): void {
	require_once __DIR__ . '/components/table.php';
	$html = \RadmanCTComponents\Components\Table\render( $data, $options );
	if ( '' === $html ) { return; }
	\RadmanCTComponents\Components\enqueue( 'table', $options );
	echo $html;
}

function rdct_particles( array $options = array() ): void {
	require_once __DIR__ . '/components/particles.php';
	\RadmanCTComponents\Components\enqueue( 'particles', $options );
	echo \RadmanCTComponents\Components\Particles\render( $options );
}

function rdct_carousel( array $items, array $options = array() ): void {
	require_once __DIR__ . '/components/carousel.php';
	$html = \RadmanCTComponents\Components\Carousel\render( $items, $options );
	if ( '' === $html ) { return; }
	\RadmanCTComponents\Components\enqueue( 'carousel', $options );
	echo $html;
}

function rdct_carousel_profile( string $profile, array $items, array $options = array() ): void {
	require_once __DIR__ . '/components/carousel/profiles.php';
	$html = \RadmanCTComponents\Components\CarouselProfiles\render( $profile, $items, $options );
	if ( '' === $html ) { return; }
	\RadmanCTComponents\Components\enqueue( 'carousel', \RadmanCTComponents\Components\CarouselProfiles\options( $profile, $options ) );
	echo $html;
}

function rdct_logo_carousel( array $items, array $options = array() ): void { rdct_carousel_profile( 'logos', $items, $options ); }
function rdct_card_carousel( array $items, array $options = array() ): void { rdct_carousel_profile( 'cards', $items, $options ); }
function rdct_slider( array $items, array $options = array() ): void { rdct_carousel_profile( 'slider', $items, $options ); }
function rdct_marquee( array $items, array $options = array() ): void { rdct_carousel_profile( 'marquee', $items, $options ); }
function rdct_stacked_cards( array $items, array $options = array() ): void { rdct_carousel_profile( 'stack', $items, $options ); }
function rdct_testimonials( array $items, array $options = array() ): void { rdct_carousel_profile( 'testimonials', $items, $options ); }
function rdct_testimonial( array $item, array $options = array() ): void {
	require_once __DIR__ . '/components/testimonial/renderer.php';
	$html = \RadmanCTComponents\Components\Testimonial\render( $item, $options );
	if ( '' === $html ) { return; }
	\RadmanCTComponents\Components\enqueue( 'testimonial', $options );
	echo $html;
}

}

function rdct_sticky_sections( array $items, array $options = array(), ?callable $aside_renderer = null, ?callable $item_renderer = null ): void {
 require_once __DIR__ . '/components/sticky-sections.php';
 $html = \RadmanCTComponents\Components\StickySections\render( $items, $options, $aside_renderer, $item_renderer );
 if ( '' !== $html ) { \RadmanCTComponents\Components\enqueue( 'sticky-sections', $options ); echo $html; }
}
function rdct_service_panels( array $items, array $options = array() ): void { rdct_sticky_sections( $items, array_replace( $options, array( 'mode' => 'services' ) ) ); }
function rdct_zoom_cards( array $items, array $options = array() ): void { rdct_sticky_sections( $items, array_replace( $options, array( 'mode' => 'zoom' ) ) ); }
function rdct_sticky_split( callable $aside_renderer, array $items, array $options = array(), ?callable $item_renderer = null ): void { rdct_sticky_sections( $items, array_replace( $options, array( 'mode' => 'split' ) ), $aside_renderer, $item_renderer ); }

function rdct_timeline( array $items, array $options = array() ): void {
 require_once __DIR__ . '/components/timeline.php';
 $html = \RadmanCTComponents\Components\Timeline\render( $items, $options );
 if ( '' === $html ) { return; }
 \RadmanCTComponents\Components\enqueue( 'timeline', $options );
 echo $html;
}

function rdct_accordion( array $items, array $options = array() ): void {
 require_once __DIR__ . '/components/accordion.php';
 $html = \RadmanCTComponents\Components\Accordion\render( $items, $options );
 if ( '' === $html ) { return; }
 \RadmanCTComponents\Components\enqueue( 'accordion', $options );
 echo $html;
}
