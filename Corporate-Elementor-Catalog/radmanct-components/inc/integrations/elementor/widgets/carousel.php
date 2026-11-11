<?php

namespace RadmanCTComponents\Elementor\Widgets;

use Elementor\Controls_Manager as Controls;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/carousel/profiles.php';

require_once __DIR__ . '/repeater-controls.php';

class Carousel extends \Elementor\Widget_Base {
	protected const PROFILE = '';
	public static function profile(): string { return static::PROFILE; }
	public function get_name() { return static::PROFILE ? 'rdct-carousel-' . static::PROFILE : 'rdct-carousel'; }
	public function get_title() { return ( static::PROFILE ? \RadmanCTComponents\Components\CarouselProfiles\definitions()[ static::PROFILE ]['title'] : __( 'Carousel', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-slider-push'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return '' !== static::PROFILE; }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_script_depends() { return array( 'rdct-elementor-components' ); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'carousel', \RadmanCTComponents\Components\CarouselProfiles\options( static::PROFILE, $this->get_settings_for_display() ) ); }

	protected function register_controls() {
		$this->start_controls_section( 'slides_content', array( 'label' => __( 'Slides', 'radmanct-components' ) ) );
		if ( 'testimonials' !== static::PROFILE ) {
		$this->add_control( 'slide_source', array( 'label' => __( 'Slide source', 'radmanct-components' ), 'type' => Controls::SELECT, 'default' => 'items', 'options' => array( 'items' => __( 'Custom slides', 'radmanct-components' ), 'gallery' => __( 'Image gallery', 'radmanct-components' ) ) ) );
		$this->add_control( 'gallery', array( 'label' => __( 'Add images', 'radmanct-components' ), 'type' => Controls::GALLERY, 'default' => array(), 'description' => __( 'Up to 50 images are displayed in selection order.', 'radmanct-components' ), 'condition' => array( 'slide_source' => 'gallery' ) ) );
		}
		$repeater = new \Elementor\Repeater();
		foreach ( \RadmanCTComponents\Components\CarouselProfiles\item_fields( static::PROFILE ) as $key => $field ) {
			if ( in_array( $key, array( 'width_tablet', 'width_mobile' ), true ) ) { continue; }
			if ( 'width' === $key ) { $repeater->add_responsive_control( $key, array( 'label' => $field['label'], 'type' => Controls::NUMBER, 'min' => 0, 'max' => 2000, 'default' => 0, 'tablet_default' => '', 'mobile_default' => '' ) ); continue; }
			$control = field_control( $key, $field );
			if ( 'testimonials' !== static::PROFILE && in_array( $key, array( 'image_id', 'content', 'link' ), true ) ) { $control['condition'] = array( 'content_type' => 'content' ); }
			$repeater->add_control( $key, $control );
		}
		$this->add_control( 'items', array( 'label' => __( 'Slides', 'radmanct-components' ), 'type' => Controls::REPEATER, 'condition' => 'testimonials' === static::PROFILE ? array() : array( 'slide_source' => 'items' ), 'fields' => $repeater->get_controls(), 'title_field' => '{{{ title }}}', 'default' => \RadmanCTComponents\Components\CarouselProfiles\sample_items( static::PROFILE ) ) );
		$this->end_controls_section();
		$fields = \RadmanCTComponents\Components\CarouselProfiles\fields( static::PROFILE );
		foreach ( \RadmanCTComponents\Components\CarouselProfiles\groups( static::PROFILE ) as $group => $spec ) {
		$this->start_controls_section( 'carousel_' . $group, array( 'label' => $spec['label'], 'tab' => 'style' === ( $spec['tab'] ?? '' ) ? Controls::TAB_STYLE : Controls::TAB_CONTENT ) );
		foreach ( $spec['fields'] as $key ) {
			$field = $fields[ $key ];
			if ( 'testimonials' === static::PROFILE && isset( \RadmanCTComponents\Components\Testimonial\presentation()[ $key ] ) ) { require_once dirname( __DIR__ ) . '/presentation.php'; \RadmanCTComponents\Integrations\Elementor\Presentation\control( $this, 'testimonial', $key, $field ); continue; }
			if ( in_array( $key, array( 'slides_tablet', 'slides_mobile', 'height_tablet', 'height_mobile', 'height_unit', 'height_unit_tablet', 'height_unit_mobile' ), true ) ) { continue; }
			$control = $field + array( 'type' => is_bool( $field['default'] ) ? Controls::SWITCHER : ( isset( $field['options'] ) ? Controls::SELECT : ( isset( $field['min'] ) ? Controls::NUMBER : Controls::TEXT ) ) );
			if ( in_array( $key, array( 'previous_icon', 'next_icon' ), true ) ) { $control['type'] = 'rdct-icon-picker'; unset( $control['options'] ); }
			if ( ! empty( $field['color'] ) ) { $control['type'] = Controls::COLOR; $control['alpha'] = false; }
			if ( is_bool( $field['default'] ) ) { $control['default'] = $field['default'] ? 'yes' : ''; }
			if ( isset( $control['condition'] ) ) { foreach ( $control['condition'] as &$values ) { $values = array_map( static fn( $value ) => is_bool( $value ) ? ( $value ? 'yes' : '' ) : $value, (array) $values ); } unset( $values ); }
			if ( 'height' === $key ) {
				$control = array( 'label' => $field['label'], 'type' => Controls::SLIDER, 'size_units' => array( 'px', 'em', '%', 'vh' ), 'range' => array( 'px' => array( 'min' => 0, 'max' => 2000 ), 'em' => array( 'min' => 0, 'max' => 100 ), '%' => array( 'min' => 0, 'max' => 100 ), 'vh' => array( 'min' => 0, 'max' => 100 ) ) );
				if ( static::PROFILE ) { foreach ( array( 'default' => 'height', 'tablet_default' => 'height_tablet', 'mobile_default' => 'height_mobile' ) as $target => $source ) { if ( $fields[ $source ]['default'] >= 0 ) { $control[ $target ] = array( 'size' => $fields[ $source ]['default'], 'unit' => 'px' ); } } }
				$this->add_responsive_control( $key, $control );
			} elseif ( 'slides' === $key ) { $control['tablet_default'] = $fields['slides_tablet']['default']; $control['mobile_default'] = $fields['slides_mobile']['default']; $this->add_responsive_control( $key, $control ); }
			else { $this->add_control( $key, $control ); }
		}
		$this->end_controls_section();
		}
	}
	protected function render() {
		$s = \RadmanCTComponents\Components\CarouselProfiles\options( static::PROFILE, $this->get_settings_for_display() );
		if ( 'testimonials' !== static::PROFILE && 'gallery' === ( $s['slide_source'] ?? 'items' ) ) { if ( static::PROFILE ) { rdct_carousel_profile( static::PROFILE, \RadmanCTComponents\Components\Carousel\gallery_items( $s['gallery'] ?? array() ), $s ); } else { rdct_carousel( \RadmanCTComponents\Components\Carousel\gallery_items( $s['gallery'] ?? array() ), $s ); } return; }
		$items = is_array( $s['items'] ?? null ) ? $s['items'] : array();
		foreach ( $items as &$item ) { $item['image_id'] = (int) ( $item['image_id']['id'] ?? 0 ); } unset( $item );
		if ( static::PROFILE ) { rdct_carousel_profile( static::PROFILE, $items, $s ); } else { rdct_carousel( $items, $s ); }
	}
}

class CardCarousel extends Carousel { protected const PROFILE = 'cards'; }
class LogoCarousel extends Carousel { protected const PROFILE = 'logos'; }
class HeroSlider extends Carousel { protected const PROFILE = 'slider'; }
class MarqueeCarousel extends Carousel { protected const PROFILE = 'marquee'; }
class StackCarousel extends Carousel { protected const PROFILE = 'stack'; }
class Testimonials extends Carousel { protected const PROFILE = 'testimonials'; }
