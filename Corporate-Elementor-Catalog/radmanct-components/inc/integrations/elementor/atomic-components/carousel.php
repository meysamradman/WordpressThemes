<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/carousel/profiles.php';
require_once dirname( __DIR__, 3 ) . '/components/testimonial/fields.php';

class CarouselItem extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type {
	public static function get_key(): string { return 'rdct-carousel-item'; }
	protected function define_shape(): array {
		$shape = array();
		foreach ( array_replace( \RadmanCTComponents\Components\Carousel\item_fields(), \RadmanCTComponents\Components\Testimonial\item_fields() ) as $key => $field ) {
			if ( ! empty( $field['media'] ) ) { $shape[ $key ] = image_field_prop(); continue; }
			if ( ! empty( $field['rich'] ) ) { $shape[ $key ] = rich_text_prop( $field['default'] ); continue; }
			if ( ! empty( $field['template'] ) ) { $shape[ $key ] = template_field_prop(); continue; }
			$shape[ $key ] = ( is_int( $field['default'] ) ? Number_Prop_Type::make() : String_Prop_Type::make() )->default( $field['default'] );
			if ( isset( $field['options'] ) ) { $shape[ $key ]->enum( array_keys( $field['options'] ) ); }
		}
		foreach ( array( 'image_url', 'background_url' ) as $key ) { $shape[ $key ] = String_Prop_Type::make()->default( '' ); }
		return $shape;
	}
}

class CarouselItems extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type {
	public static function get_key(): string { return 'rdct-carousel-items'; }
	protected function define_item_type(): \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type { return CarouselItem::make(); }
	protected function validate_value( $value ): bool { return is_array( $value ) && count( $value ) <= 50 && parent::validate_value( $value ); }
}

class CarouselItemsControl extends \Elementor\Modules\AtomicWidgets\Controls\Base\Atomic_Control_Base {
	public function get_type(): string { return 'rdct-carousel-items'; }
	public function get_props(): array { return array(); }
}

class TestimonialItemsControl extends CarouselItemsControl { public function get_type(): string { return 'rdct-testimonial-items'; } }
class ProfileItemsControl extends CarouselItemsControl {
	private string $profile = '';
	public function configure( string $profile ): self { $this->profile = $profile; return $this; }
	public function get_type(): string { return 'rdct-carousel-items-' . $this->profile; }
}

class Carousel extends Component {
	protected const PROFILE = '';
	public static function profile(): string { return static::PROFILE; }
	public function show_in_panel() { return '' !== static::PROFILE; }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public static function get_element_type(): string { return static::PROFILE ? 'rdct-atomic-carousel-' . static::PROFILE : 'rdct-atomic-carousel'; }
	protected static function component(): string { return static::PROFILE ? 'carousel-' . static::PROFILE : 'carousel'; }
	public function get_title() { return ( static::PROFILE ? \RadmanCTComponents\Components\CarouselProfiles\definitions()[ static::PROFILE ]['title'] : __( 'Carousel', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-slider-push'; }
	public static function typed_items( array $items ): array {
		$shape = CarouselItem::make()->get_shape(); $typed = array();
		foreach ( array_slice( $items, 0, 50 ) as $row ) {
			$fields = array();
			foreach ( $shape as $key => $prop ) { if ( array_key_exists( $key, $row ) ) { $fields[ $key ] = typed_field( $prop, $row[ $key ] ); } }
			$typed[] = CarouselItem::generate( $fields );
		}
		return $typed;
	}
	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		foreach ( array( 'height', 'height_tablet', 'height_mobile' ) as $key ) { $schema[ $key ] = \Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type::make()->units( array( 'px', 'em', '%', 'vh', 'auto' ) )->default_unit( 'px' ); }
		$schema['items'] = CarouselItems::make()->default( static::typed_items( \RadmanCTComponents\Components\CarouselProfiles\sample_items( static::PROFILE ) ) );
		foreach ( \RadmanCTComponents\Components\CarouselProfiles\definitions()[ static::PROFILE ]['defaults'] ?? array() as $key => $value ) { if ( in_array( $key, array( 'height', 'height_tablet', 'height_mobile' ), true ) ) { $schema[ $key ]->default( array( 'size' => $value, 'unit' => 'px' ) ); } }
		return $schema;
	}
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'carousel', \RadmanCTComponents\Components\CarouselProfiles\options( static::PROFILE, $this->get_atomic_settings() ) ); }
	public function get_script_depends() { return \RadmanCTComponents\Components\script_depends( 'carousel', array(), $this->is_type_instance() ); }
	protected function get_templates(): array { return array( 'radmanct/atomic/' . static::component() => __DIR__ . '/carousel.html.twig' ); }
	protected function define_atomic_controls(): array {
        $controls = parent::define_atomic_controls()[0]->get_items();
        $fields = \RadmanCTComponents\Components\CarouselProfiles\fields( static::PROFILE );
        $controls = array_values( array_filter( $controls, static fn( $control ) => empty( $fields[ $control->get_bind() ]['internal'] ) ) );
        $controls = array_values( array_filter( $controls, static fn( $control ) => ! str_starts_with( $control->get_bind(), 'height_unit' ) ) );
        foreach ( $controls as &$control ) {
            $key = $control->get_bind();
            if ( in_array( $key, array( 'height', 'height_tablet', 'height_mobile' ), true ) ) { $control = \Elementor\Modules\AtomicWidgets\Controls\Types\Size_Control::bind_to( $key )->set_units( array( 'px', 'em', '%', 'vh', 'auto' ) )->set_default_unit( 'px' )->set_disable_custom( true )->set_placeholder( __( 'Inherit', 'radmanct-components' ) )->set_label( 'height' === $key ? __( 'Item height', 'radmanct-components' ) : ( 'height_tablet' === $key ? __( 'Tablet item height', 'radmanct-components' ) : __( 'Mobile item height', 'radmanct-components' ) ) ); }
        } unset( $control );
        $item_control = 'testimonials' === static::PROFILE ? TestimonialItemsControl::bind_to( 'items' ) : ( static::PROFILE ? ProfileItemsControl::bind_to( 'items' )->configure( static::PROFILE ) : CarouselItemsControl::bind_to( 'items' ) );
        $sections = array( \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_carousel_slides' )->set_label( __( 'Slides', 'radmanct-components' ) )->set_items( array( $item_control->set_label( __( 'Slides', 'radmanct-components' ) ) ) ) );
        foreach ( \RadmanCTComponents\Components\CarouselProfiles\groups( static::PROFILE ) as $key => $group ) {
            $items = array_values( array_filter( $controls, static fn( $control ) => in_array( preg_replace( '/_responsive$/', '', $control->get_bind() ), $group['fields'], true ) ) );
            $sections[] = \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_carousel_' . $key )->set_label( $group['label'] )->set_items( $items );
        }
        return $sections;
    }
}

class CardCarousel extends Carousel { protected const PROFILE = 'cards'; }
class LogoCarousel extends Carousel { protected const PROFILE = 'logos'; }
class HeroSlider extends Carousel { protected const PROFILE = 'slider'; }
class MarqueeCarousel extends Carousel { protected const PROFILE = 'marquee'; }
class StackCarousel extends Carousel { protected const PROFILE = 'stack'; }
class Testimonials extends Carousel { protected const PROFILE = 'testimonials'; }
