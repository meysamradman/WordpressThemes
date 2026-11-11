<?php

namespace RadmanCTComponents\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

function classic_in_panel(): bool {
	return true;
}

function register( $manager ): void {
	require_once dirname( __DIR__, 2 ) . '/icons.php';
	require_once __DIR__ . '/widgets/icon.php';
	require_once __DIR__ . '/widgets/text-reveal.php';
	require_once __DIR__ . '/widgets/curtain.php';
	require_once __DIR__ . '/widgets/timeline.php';
	require_once __DIR__ . '/widgets/accordion.php';
	require_once __DIR__ . '/widgets/sticky-sections.php';
	require_once __DIR__ . '/widgets/box.php';
	require_once __DIR__ . '/widgets/flipbox.php';
	require_once __DIR__ . '/widgets/icon-box.php';
	require_once __DIR__ . '/widgets/button.php';
	require_once __DIR__ . '/widgets/image.php';
	require_once __DIR__ . '/widgets/table.php';
	require_once __DIR__ . '/widgets/carousel.php';
	require_once __DIR__ . '/widgets/testimonial.php';
	require_once __DIR__ . '/widgets/particles.php';
	require_once __DIR__ . '/widgets/content-ui.php';
	$manager->register( new TaxonomyList() );
	$manager->register( new ArchiveSearch() );
	$manager->register( new Icon() );
	$manager->register( new TextReveal() );
	$manager->register( new Curtain() );
	$manager->register( new Timeline() );
	$manager->register( new Accordion() );
	$manager->register( new StickySections() );
	foreach ( array( StickyStack::class, StickyOverlap::class, ServicePanels::class, ZoomCards::class, StickySplit::class ) as $class ) { $manager->register( new $class() ); }
	$manager->register( new Box() );
	$manager->register( new FlipBox() );
	$manager->register( new IconBox() );
	$manager->register( new Button() );
	$manager->register( new Image() );
	$manager->register( new Table() );
	$manager->register( new Carousel() );
	$manager->register( new Testimonial() );
	foreach ( array( CardCarousel::class, LogoCarousel::class, HeroSlider::class, MarqueeCarousel::class, StackCarousel::class, Testimonials::class ) as $class ) { $manager->register( new $class() ); }
	$manager->register( new Particles() );
}

add_action( 'elementor/widgets/register', __NAMESPACE__ . '\\register' );
require_once __DIR__ . '/panel.php';
