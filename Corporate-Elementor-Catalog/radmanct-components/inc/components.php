<?php

namespace RadmanCTComponents\Components;

defined( 'ABSPATH' ) || exit;

function definitions(): array {
	$definitions = array(
		'form-controls' => array( 'handle' => 'rdct-form-controls', 'entry' => 'src/css/controls/forms.css' ),
		'select-control' => array( 'handle' => 'rdct-select', 'entry' => 'src/css/controls/select.css' ),
		'combobox' => array( 'handle' => 'rdct-combobox', 'entry' => 'src/css/controls/combobox.css' ),
		'category-tree' => array( 'handle' => 'rdct-category-tree', 'entry' => 'src/css/controls/category-tree.css' ),
		'taxonomy-chips' => array( 'handle' => 'rdct-taxonomy-chips', 'entry' => 'src/css/components/taxonomy-chips.css' ),
		'archive-search' => array( 'handle' => 'rdct-archive-search', 'entry' => 'src/css/components/archive-search.css' ),
		'card-preview' => array( 'handle' => 'rdct-card-preview', 'entry' => 'src/css/components/card-preview.css' ),
		'page-title' => array( 'handle' => 'rdct-page-title', 'entry' => 'src/css/components/page-title.css' ),
		'page-title-notched' => array( 'handle' => 'rdct-page-title-notched', 'entry' => 'src/css/components/page-title-notched.css' ),

		'accordion' => array( 'handle' => 'rdct-accordion', 'entry' => 'src/css/components/accordion/base.css' ),
		'accordion-lined' => array( 'handle' => 'rdct-accordion-lined', 'entry' => 'src/css/components/accordion/lined.css' ),
		'accordion-boxed' => array( 'handle' => 'rdct-accordion-boxed', 'entry' => 'src/css/components/accordion/boxed.css' ),
		'timeline' => array( 'handle' => 'rdct-timeline', 'entry' => 'src/css/components/timeline/base.css' ),
		'timeline-alternating' => array( 'handle' => 'rdct-timeline-alternating', 'entry' => 'src/css/components/timeline/alternating.css' ),
		'button' => array( 'handle' => 'rdct-button', 'entry' => 'src/css/components/button/base.css' ),
		'sticky-sections' => array( 'handle' => 'rdct-sticky-sections', 'entry' => 'src/css/components/sticky-sections.css' ),
		'sticky-sections-stack' => array( 'handle' => 'rdct-sticky-sections-stack', 'entry' => 'src/css/components/sticky-sections/stack.css' ),
		'sticky-sections-panels' => array( 'handle' => 'rdct-sticky-sections-panels', 'entry' => 'src/css/components/sticky-sections/panels.css' ),
		'sticky-sections-split' => array( 'handle' => 'rdct-sticky-sections-split', 'entry' => 'src/css/components/sticky-sections/split.css' ),
		'particles' => array( 'handle' => 'rdct-particles', 'entry' => 'src/css/components/particles.css' ),
		'marquee' => array( 'handle' => 'rdct-marquee', 'entry' => 'src/css/components/marquee.css' ),
		'hover-border' => array( 'handle' => 'rdct-hover-border', 'entry' => 'src/css/components/hover-border.css' ),
		'carousel-fade' => array( 'handle' => 'rdct-carousel-fade', 'entry' => 'src/css/components/carousel/fade.css' ),
		'carousel-stack' => array( 'handle' => 'rdct-carousel-stack', 'entry' => 'src/css/components/carousel/stack.css' ),
		'carousel-motion' => array( 'handle' => 'rdct-carousel-motion', 'entry' => 'src/css/components/carousel/motion.css' ),
		'carousel-width' => array( 'handle' => 'rdct-carousel-width', 'entry' => 'src/css/components/carousel/width.css' ),
		'carousel-navigation-surface' => array( 'handle' => 'rdct-carousel-navigation-surface', 'entry' => 'src/css/components/carousel/navigation-surface.css' ),
		'carousel-navigation-shadow' => array( 'handle' => 'rdct-carousel-navigation-shadow', 'entry' => 'src/css/components/carousel/navigation-shadow.css' ),
		'carousel-navigation' => array( 'handle' => 'rdct-carousel-navigation', 'entry' => 'src/css/components/carousel/navigation.css' ),
		'carousel' => array( 'handle' => 'rdct-carousel', 'entry' => 'src/css/components/carousel/base.css' ),
		'carousel-hero' => array( 'handle' => 'rdct-carousel-hero', 'entry' => 'src/css/components/carousel/hero.css' ),
		'testimonial' => array( 'handle' => 'rdct-testimonial', 'entry' => 'src/css/components/testimonial/base.css' ),
		'table' => array( 'handle' => 'rdct-table', 'entry' => 'src/css/components/table/base.css' ),
		'image' => array( 'handle' => 'rdct-image', 'entry' => 'src/css/components/image/base.css' ),
		'image-lightbox' => array( 'handle' => 'rdct-image-lightbox', 'entry' => 'src/css/components/image/lightbox.css' ),
		'icon-box' => array( 'handle' => 'rdct-icon-box', 'entry' => 'src/css/components/icon-box/base.css' ),
		'icon-box-animation' => array( 'handle' => 'rdct-icon-box-animation', 'entry' => 'src/css/components/icon-box/animation.css' ),
		'icons' => array( 'handle' => 'rdct-icons', 'entry' => 'src/css/components/icons.css' ),
		'text-segments' => array( 'handle' => 'rdct-text-segments', 'entry' => 'src/css/components/text-segments.css' ),
		'text-effects' => array( 'handle' => 'rdct-text-effects', 'entry' => 'src/css/components/text-effects.css' ),
		'text-reveal' => array( 'handle' => 'rdct-text-reveal', 'entry' => 'src/css/components/text-reveal.css' ),
		'curtain' => array( 'handle' => 'rdct-curtain', 'entry' => 'src/css/components/curtain.css' ),
		'curtain-images' => array( 'handle' => 'rdct-curtain-images', 'entry' => 'src/css/components/curtain/images.css' ),
		'flipbox' => array( 'handle' => 'rdct-flipbox', 'entry' => 'src/css/components/flipbox/base.css' ),
		'box' => array( 'handle' => 'rdct-box', 'entry' => 'src/css/components/box/base.css' ),
	);
	foreach ( array( 'solid', 'outline', 'plain', 'underline', 'text', 'switch', 'marquee', 'liquid', 'icon', 'icon-motion', 'ripple', 'sticky', 'dialog' ) as $feature ) { $definitions[ 'button-' . $feature ] = array( 'handle' => 'rdct-button-' . $feature, 'entry' => 'src/css/components/button/' . $feature . '.css' ); }
	foreach ( array( 'cover', 's01', 's01a', 's01b', 's02', 's03', 's04', 's05', 's06', 's07', 's08', 's09', 's10', 's11' ) as $style ) { $definitions[ 'box-' . $style ] = array( 'handle' => 'rdct-box-' . $style, 'entry' => 'src/css/components/box/' . $style . '.css' ); }
	foreach ( array( 'lr', 'rl', 'tb', 'bt' ) as $direction ) { $definitions[ 'flipbox-' . $direction ] = array( 'handle' => 'rdct-flipbox-' . $direction, 'entry' => 'src/css/components/flipbox/' . $direction . '.css' ); }
	foreach ( array( 'top', 'inline', 'side' ) as $layout ) { $definitions[ 'icon-box-' . $layout ] = array( 'handle' => 'rdct-icon-box-' . $layout, 'entry' => 'src/css/components/icon-box/' . $layout . '.css' ); }
	foreach ( array( 'grid', 'striped', 'minimal' ) as $variant ) { $definitions[ 'table-' . $variant ] = array( 'handle' => 'rdct-table-' . $variant, 'entry' => 'src/css/components/table/' . $variant . '.css' ); }
	foreach ( array( 'cards', 'images', 'brands' ) as $variant ) { $definitions[ 'carousel-' . $variant ] = array( 'handle' => 'rdct-carousel-' . $variant, 'entry' => 'src/css/components/carousel/' . $variant . '.css' ); }
	require_once __DIR__ . '/components/testimonial/fields.php';
	foreach ( array_keys( Testimonial\styles() ) as $style ) { $definitions[ 'testimonial-' . $style ] = array( 'handle' => 'rdct-testimonial-' . $style, 'entry' => 'src/css/components/testimonial/' . $style . '.css' ); }
	foreach ( array( 'header-state', 'inline-image', 'segment-gradient', 'segment-mask', 'highlight', 'fill', 'rotate', 'rotate-list', 'highlight-fill', 'highlight-wave', 'highlight-double', 'highlight-circle', 'highlight-brush' ) as $style ) { $definitions[ 'text-' . $style ] = array( 'handle' => 'rdct-text-' . $style, 'entry' => 'src/css/components/text/' . $style . '.css' ); }
	foreach ( array( 'parallax', 'reveal', 'float', 'tilt', 'shadow', 'label' ) as $feature ) { $definitions[ 'image-' . $feature ] = array( 'handle' => 'rdct-image-' . $feature, 'entry' => 'src/css/components/image/' . $feature . '.css' ); }
	return $definitions;
}

function requirements( string $name, array $options = array() ): array {
	$name = 'icon' === $name ? 'icons' : $name;
	$styles = array();
	switch ( $name ) {
		case 'card-preview':
			$styles = array( 'card-preview' );
			break;
		case 'taxonomy-list':
			require_once __DIR__ . '/components/taxonomy-list.php';
			$styles = TaxonomyList\assets( $options );
			break;
		case 'archive-search':
			$styles = array( 'form-controls', 'archive-search' );
			break;
		case 'accordion':
			require_once __DIR__ . '/components/accordion.php';
			$styles = Accordion\assets( $options );
			break;
		case 'timeline':
			require_once __DIR__ . '/components/timeline.php';
			$styles = Timeline\assets( $options );
			break;
		case 'button':
			require_once __DIR__ . '/components/button.php';
			$styles = Button\assets( $options );
			break;
		case 'curtain':
			require_once __DIR__ . '/components/curtain.php';
			$styles = array( 'images' === Curtain\settings( $options )['layout'] ? 'curtain-images' : 'curtain' );
			break;
		case 'sticky-sections':
			require_once __DIR__ . '/components/sticky-sections.php';
			$mode = StickySections\settings( $options )['mode'];
			$styles = array( 'sticky-sections', 'sticky-sections-' . ( in_array( $mode, array( 'stack', 'zoom' ), true ) ? 'stack' : ( 'split' === $mode ? 'split' : 'panels' ) ) );
			if ( 'overlap' === $mode ) { $styles = array( 'sticky-sections' ); }
			break;
		case 'box':
			require_once __DIR__ . '/components/box.php';
			$styles = array( 'box', 'box-' . Box\settings( $options )['template'] );
			break;
		case 'flipbox':
			require_once __DIR__ . '/components/flipbox.php';
			$styles = array( 'flipbox', 'flipbox-' . FlipBox\settings( $options )['direction'] );
			break;
		case 'table':
			require_once __DIR__ . '/components/table.php';
			$styles = array( 'table', 'table-' . Table\settings( $options )['variant'] );
			break;
		case 'icon-box':
			require_once __DIR__ . '/components/icon-box.php';
			$s = IconBox\settings( $options );
			$styles = array( 'icon-box', 'icon-box-' . $s['layout'] );
			if ( IconBox\has_media( $s ) ) {
				if ( 'icon' === $s['icon_type'] ) { $styles[] = 'icons'; }
				if ( 'none' !== $s['animation'] ) { $styles[] = 'icon-box-animation'; }
			}
			break;
		case 'image':
		case 'carousel':
		case 'text-reveal':
			if ( 'carousel' === $name && is_string( $options['carousel_profile'] ?? null ) && '' !== $options['carousel_profile'] ) { require_once __DIR__ . '/components/carousel/profiles.php'; $styles = CarouselProfiles\assets( $options['carousel_profile'], $options ); break; }
			require_once __DIR__ . '/components/' . $name . '.php';
			$callback = __NAMESPACE__ . '\\' . array( 'image' => 'Image', 'carousel' => 'Carousel', 'text-reveal' => 'TextReveal' )[ $name ] . '\\assets';
			$styles = $callback( $options );
			break;
		case 'page-title':
			$styles = 'notched' === ( $options['shape'] ?? '' ) ? array( 'page-title', 'page-title-notched' ) : array( 'page-title' );
			break;
		case 'testimonial':
			require_once __DIR__ . '/components/testimonial/fields.php';
			$styles = array( 'testimonial', 'testimonial-' . Testimonial\style( $options['testimonial_style'] ?? '' ) );
			break;
		default:
			if ( isset( definitions()[ $name ] ) ) { $styles = array( $name ); }
	}
	return array( 'styles' => $styles, 'script' => needs_script( $name, $options ) );
}

function style_depends( string $name, array $options = array() ): array {
	$definitions = definitions();
	return array_values( array_map( static fn( $key ) => $definitions[ $key ]['handle'], requirements( $name, $options )['styles'] ) );
}

function enqueue( string $name, array $options = array() ): void {
	$requirements = requirements( $name, $options );
	foreach ( $requirements['styles'] as $style ) { use_component( $style ); print_late_style( $style ); }
	if ( $requirements['script'] ) { \RadmanCTComponents\Assets\frontend(); }
}

function needs_script( string $name, array $options = array() ): bool {
	if ( 'button' === $name ) { require_once __DIR__ . '/components/button.php'; $s = Button\display_settings( $options ); return ! $s['disabled'] && ( 'link' !== $s['action'] || $s['magnetic'] || $s['icon_magnetic'] || str_starts_with( $s['text_effect'], 'liquid-' ) ); }
	if ( 'box' === $name ) {
		require_once __DIR__ . '/components/box.php';
		return 's06' === \RadmanCTComponents\Components\Box\settings( $options )['template'];
	}
	if ( 'image' === $name ) {
		require_once __DIR__ . '/components/image.php';
		$s = \RadmanCTComponents\Components\Image\settings( $options );
		return $s['parallax'] || $s['float'] || $s['animate_shadow'] || $s['reveal'] || $s['tilt'] || $s['lightbox'];
	}
	if ( 'text-reveal' === $name ) {
		if ( ! empty( $options['header_colors'] ) ) { return true; }
		$mode = $options['mode'] ?? 'scroll';
		$mode = in_array( $mode, array( 'scroll', 'fill', 'rotate', 'marquee', 'static', 'hover', 'rich' ), true ) ? $mode : 'scroll';
		return in_array( $mode, array( 'scroll', 'fill', 'rotate', 'marquee' ), true );
	}
	return in_array( $name, array( 'particles', 'carousel', 'curtain', 'flipbox', 'sticky-sections', 'card-preview' ), true );
}

function script_depends( string $name, array $options = array(), bool $type_instance = false ): array {
	if ( in_array( $name, array( 'taxonomy-list', 'archive-search', 'accordion', 'timeline', 'icon', 'icons', 'table', 'icon-box', 'testimonial' ), true ) ) { return array(); }
	return $type_instance || needs_script( $name, $options ) ? array( 'rdct-elementor-components' ) : array();
}

function register_assets(): void {
	$base = \RadmanCTComponents\Assets\entry( 'src/css/base.css' );
	if ( $base ) { wp_register_style( 'rdct-components-base', \RadmanCTComponents\Assets\url( $base['file'] ), array(), null ); }
	foreach ( definitions() as $definition ) {
		if ( wp_style_is( $definition['handle'], 'registered' ) ) { continue; }
		if ( 'rdct-button-text' === $definition['handle'] ) {
			wp_register_style( $definition['handle'], false, array( 'rdct-button' ), null );
			continue;
		}
		$asset = \RadmanCTComponents\Assets\entry( $definition['entry'] );
		if ( $asset ) {
			wp_register_style( $definition['handle'], \RadmanCTComponents\Assets\url( $asset['file'] ), 'rdct-icons' === $definition['handle'] ? array() : array( 'rdct-components-base' ), null );
		}
	}
	$script = \RadmanCTComponents\Assets\entry( 'src/js/integrations/elementor-components.js' );
	\RadmanCTComponents\Assets\register_frontend();
	if ( $script ) {
		wp_register_script( 'rdct-elementor-components', \RadmanCTComponents\Assets\url( $script['file'] ), array( 'elementor-frontend', 'rdct-components' ), null, true );
	}
}

function use_component( string $name ): void {
	$definition = definitions()[ $name ] ?? null;
	if ( ! $definition ) {
		return;
	}
	if ( ! wp_style_is( $definition['handle'], 'registered' ) ) {
		register_assets();
	}
	wp_enqueue_style( $definition['handle'] );
}

function print_late_style( string $name ): void {
	$definition = definitions()[ $name ] ?? null;
	if ( $definition && did_action( 'wp_head' ) && ! wp_style_is( $definition['handle'], 'done' ) ) {
		wp_print_styles( array( $definition['handle'] ) );
	}
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\register_assets', 9 );
add_filter( 'wp_script_attributes', static function ( array $attributes ): array {
	if ( 'rdct-elementor-components-js' === ( $attributes['id'] ?? '' ) ) {
		$attributes['type'] = 'module';
	}
	return $attributes;
} );
