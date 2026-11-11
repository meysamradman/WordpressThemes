<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Number_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Textarea_Control;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/presentation.php';
require_once __DIR__ . '/native-fields.php';
require_once dirname( __DIR__ ) . '/safe-attributes.php';

class ColorControl extends \Elementor\Modules\AtomicWidgets\Controls\Base\Atomic_Control_Base {
	public function get_type(): string { return 'rdct-color'; }
	public function get_props(): array { return array(); }
}

abstract class Component extends \Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Widget_Base {
	use \Elementor\Modules\AtomicWidgets\Elements\Base\Has_Template { get_shared_templates as private native_shared_templates; }

	abstract protected static function component(): string;
	protected function get_shared_templates(): array { return $this->native_shared_templates() + array( 'radmanct/attributes' => dirname( __DIR__ ) . '/safe-attributes.html.twig' ); }
	public function get_categories(): array { return array( 'radmanct-atomic' ); }
	public static function html_tag_follows_link(): bool { return false; }
	public static function get_computed_html_tag( array $settings ): string { return 'div'; }
	public function get_style_depends() { return 'sticky-sections' === static::component() ? array( 'rdct-sticky-sections' ) : \RadmanCTComponents\Components\style_depends( static::component() ); }
	public function get_script_depends() { return \RadmanCTComponents\Components\script_depends( static::component(), $this->is_type_instance() ? array() : $this->get_atomic_settings(), $this->is_type_instance() ); }

	protected static function define_props_schema(): array {
		$schema = array(
			'classes' => \Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type::make()->default( array() ),
			'attributes' => \Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type::make(),
		);
		foreach ( fields( static::component() ) as $key => $field ) {
			$default = $field['default'];
			$prop = is_bool( $default ) ? Boolean_Prop_Type::make() : ( is_numeric( $default ) ? Number_Prop_Type::make()->float() : String_Prop_Type::make() );
			if ( ! empty( $field['rich'] ) ) { $prop = rich_text_prop( $default ); }
			if ( ! empty( $field['template'] ) ) { $prop = template_field_prop(); }
			if ( isset( $field['options'] ) ) { $prop->enum( is_string( $default ) ? array_map( 'strval', array_keys( $field['options'] ) ) : array_keys( $field['options'] ) ); }
			$schema[ $key ] = ! empty( $field['rich'] ) || ! empty( $field['template'] ) ? $prop : $prop->default( $default );
			if ( isset( $field['condition'] ) ) {
				$rules = \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make( 'and' );
				foreach ( $field['condition'] as $path => $values ) { $rules->where( array( 'operator' => 'in', 'path' => array( $path ), 'value' => (array) $values, 'effect' => 'hide' ) ); }
				$schema[ $key ]->set_dependencies( $rules->get() );
			}
			if ( isset( $field['styles'] ) ) { $schema[ $key ]->set_dependencies( \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make()->where( array( 'operator' => 'in', 'path' => array( 'template' ), 'value' => $field['styles'], 'effect' => 'hide' ) )->get() ); }
		}
		foreach ( \RadmanCTComponents\Components\Presentation\controls( static::component() ) as $key => $spec ) {
			$prop = ResponsiveValues::make();
			if ( isset( $spec['condition'] ) ) {
				$rules = \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make( 'and' );
				foreach ( $spec['condition'] as $path => $values ) { $rules->where( array( 'operator' => 'in', 'path' => array( $path ), 'value' => (array) $values, 'effect' => 'hide' ) ); }
				$prop->set_dependencies( $rules->get() );
			}
			$schema[ $key . '_responsive' ] = $prop;
		}
		return $schema;
	}

	protected function define_atomic_controls(): array {
		$items = array();
		foreach ( fields( static::component() ) as $key => $field ) {
			if ( in_array( $key, array( 'icon', 'name', 'previous_icon', 'next_icon' ), true ) && isset( $field['options'] ) ) {
				$control = LocalIconControl::bind_to( $key );
			} elseif ( isset( \RadmanCTComponents\Components\Presentation\controls( static::component() )[ $key ] ) ) {
				$control = ResponsiveControl::bind_to( $key . '_responsive' )->configure( static::component(), $key );
			} elseif ( ! empty( $field['color'] ) ) {
				$control = ColorControl::bind_to( $key );
			} elseif ( ! empty( $field['rich'] ) ) {
				$control = RichTextControl::bind_to( $key );
			} elseif ( isset( $field['options'] ) ) {
				$options = array();
				foreach ( $field['options'] as $value => $label ) { $options[] = array( 'value' => $value, 'label' => $label ); }
				$control = Select_Control::bind_to( $key )->set_options( $options );
			} elseif ( is_bool( $field['default'] ) ) {
				$control = Switch_Control::bind_to( $key );
			} elseif ( isset( $field['min'] ) ) {
				$control = Number_Control::bind_to( $key )->set_should_force_int( false )->set_min( is_int( $field['min'] ) ? $field['min'] : (int) floor( $field['min'] ) )->set_max( is_int( $field['max'] ) ? $field['max'] : (int) ceil( $field['max'] ) );
			} else {
				$control = ! empty( $field['multiline'] ) ? Textarea_Control::bind_to( $key ) : Text_Control::bind_to( $key );
			}
			if ( isset( $field['description'] ) ) { $control->set_description( $field['description'] ); }
			$items[] = $control->set_label( $field['label'] );
		}
		foreach ( \RadmanCTComponents\Components\Presentation\controls( static::component() ) as $key => $spec ) { if ( ! isset( fields( static::component() )[ $key ] ) ) { $items[] = ResponsiveControl::bind_to( $key . '_responsive' )->configure( static::component(), $key )->set_label( $spec['label'] ); } }
		return array( Section::make()->set_id( 'rdct_content' )->set_label( __( 'Content', 'radmanct-components' ) )->set_items( $items ) );
	}

	protected function get_templates(): array { return array( 'radmanct/atomic/' . static::component() => __DIR__ . '/' . static::component() . '.html.twig' ); }

	protected function render() {
		$settings = $this->get_atomic_settings();
		$classes = array_filter( $settings['classes'] ?? array(), 'is_string' );
		if ( in_array( static::component(), array( 'carousel', 'box' ), true ) || str_starts_with( static::component(), 'carousel-' ) ) { $classes[] = 'rdct-atomic-surface'; }
		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-interaction-id="' . esc_attr( $this->get_interaction_id() ) . '"';
		if ( ! empty( $settings['_cssid'] ) ) { echo ' id="' . esc_attr( $settings['_cssid'] ) . '"'; }
		echo ' ' . \RadmanCTShared\Elementor\Attributes\sanitize( $settings['attributes'] ?? '' ) . '>';
		if ( 'taxonomy-list' === static::component() ) {
			rdct_taxonomy_list( $settings );
		} elseif ( 'archive-search' === static::component() ) {
			rdct_archive_search( $settings );
		} elseif ( 'accordion' === static::component() ) {
			rdct_accordion( is_array( $settings['items'] ?? null ) ? $settings['items'] : array(), $settings );
		} elseif ( 'timeline' === static::component() ) {
			rdct_timeline( is_array( $settings['items'] ?? null ) ? $settings['items'] : array(), $settings );
		} elseif ( 'button' === static::component() ) {
			rdct_button( $settings );
		} elseif ( 'testimonial' === static::component() ) {
			rdct_testimonial( $settings, $settings );
		} elseif ( 'particles' === static::component() ) {
			rdct_particles( $settings );
		} elseif ( str_starts_with( static::component(), 'carousel-' ) ) {
			rdct_carousel_profile( substr( static::component(), 9 ), is_array( $settings['items'] ?? null ) ? $settings['items'] : array(), $settings );
		} elseif ( 'carousel' === static::component() ) {
			rdct_carousel( is_array( $settings['items'] ?? null ) ? $settings['items'] : array(), $settings );
		} elseif ( 'table' === static::component() ) {
			rdct_table( is_array( $settings['data'] ?? null ) ? $settings['data'] : array(), $settings );
		} elseif ( 'icon' === static::component() ) {
			rdct_icon( is_string( $settings['name'] ?? null ) ? $settings['name'] : '', $settings );
		} elseif ( 'sticky-sections' === static::component() ) {
			rdct_sticky_sections( is_array( $settings['items'] ?? null ) ? $settings['items'] : array(), $settings );
		} elseif ( 'curtain' === static::component() ) {
			rdct_curtain( is_array( $settings['items'] ?? null ) ? $settings['items'] : array(), $settings );
		} elseif ( 'image' === static::component() ) {
			$settings['image_id'] = (int) ( $settings['image']['id'] ?? 0 );
			$settings['overlay_image_id'] = (int) ( $settings['overlay_image']['id'] ?? 0 );
			rdct_image( $settings );
		} elseif ( 'icon-box' === static::component() ) {
			$settings['image_id'] = (int) ( $settings['image']['id'] ?? 0 );
			rdct_icon_box( $settings );
		} elseif ( 'flipbox' === static::component() ) {
			foreach ( array( 'front', 'back' ) as $side ) { $settings[ $side . '_image_id' ] = (int) ( $settings[ $side . '_image' ]['id'] ?? 0 ); }
			rdct_flipbox( $settings );
		} elseif ( 'box' === static::component() ) {
			$settings['image_id'] = (int) ( $settings['image']['id'] ?? 0 );
			rdct_box( $settings );
		} else {
			rdct_text_reveal( is_string( $settings['text'] ?? null ) ? $settings['text'] : '', $settings );
		}
		echo '</div>';
	}
}

class Icon extends Component {
	public static function get_element_type(): string { return 'rdct-atomic-icon'; }
	protected static function component(): string { return 'icon'; }
	public function get_title() { return ( __( 'Icon', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-favorite'; }
	protected function define_atomic_controls(): array {
		$content = $style = array();
		foreach ( parent::define_atomic_controls()[0]->get_items() as $control ) { if ( str_ends_with( $control->get_bind(), '_responsive' ) ) { $style[] = $control; } else { $content[] = $control; } }
		return array( Section::make()->set_id( 'rdct_icon_content' )->set_label( __( 'Icon', 'radmanct-components' ) )->set_items( $content ), Section::make()->set_id( 'rdct_icon_style' )->set_label( __( 'Style', 'radmanct-components' ) )->set_items( $style ) );
	}
}

require_once __DIR__ . '/text.php';
