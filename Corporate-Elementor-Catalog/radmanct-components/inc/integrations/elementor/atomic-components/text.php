<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;

defined( 'ABSPATH' ) || exit;

class TextReveal extends Component {
	public static function get_element_type(): string { return 'rdct-atomic-text-reveal'; }
	protected static function component(): string { return 'text-reveal'; }
	public function get_title() { return ( __( 'Text', 'radmanct-components' ) ); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'text-reveal', $this->get_atomic_settings() ); }
	public function get_keywords() { return array( 'text', 'marquee', 'ticker', 'reveal', 'headline', __( 'Marquee', 'radmanct-components' ), __( 'Rotating words', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-animated-headline'; }
	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		$schema['items'] = TextWords::make()->default( array() )->set_dependencies( \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make()->where( array( 'operator' => 'eq', 'path' => array( 'mode' ), 'value' => 'rotate', 'effect' => 'hide' ) )->get() );
		$schema['segments'] = TextSegments::make()->default( array() )->set_dependencies( \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make()->where( array( 'operator' => 'eq', 'path' => array( 'mode' ), 'value' => 'rich', 'effect' => 'hide' ) )->get() );
		return $schema;
	}
	protected function define_atomic_controls(): array {
		$controls = parent::define_atomic_controls()[0]->get_items(); $sections = array();
		foreach ( \RadmanCTComponents\Components\TextReveal\groups() as $key => $group ) {
			$items = array_values( array_filter( $controls, static fn( $control ) => in_array( str_replace( '_responsive', '', $control->get_bind() ), $group['fields'], true ) || ( 'appearance' === $key && in_array( $control->get_bind(), array( 'text_size_responsive', 'word_size_responsive', 'align_responsive' ), true ) ) ) );
			if ( 'segments' === $key ) { $items[] = TextSegmentsControl::bind_to( 'segments' )->set_label( __( 'Text segments', 'radmanct-components' ) ); }
			if ( 'words' === $key ) { $items[] = TextWordsControl::bind_to( 'items' )->set_label( __( 'Rotating words', 'radmanct-components' ) ); }
			$sections[] = Section::make()->set_id( 'rdct_text_' . $key )->set_label( $group['label'] )->set_items( $items );
		}
		return $sections;
	}
}

class TextWord extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type {
	public static function get_key(): string { return 'rdct-text-word'; }
	protected function define_shape(): array { return array( 'title' => String_Prop_Type::make()->default( '' ), 'color' => String_Prop_Type::make()->default( '' ) ); }
}

class TextWords extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type {
	public static function get_key(): string { return 'rdct-text-words'; }
	protected function define_item_type(): \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type { return TextWord::make(); }
	protected function validate_value( $value ): bool { return is_array( $value ) && count( $value ) <= 30 && parent::validate_value( $value ); }
}

class TextWordsControl extends \Elementor\Modules\AtomicWidgets\Controls\Base\Atomic_Control_Base {
	public function get_type(): string { return 'rdct-text-words'; }
	public function get_props(): array { return array(); }
}

class TextSegment extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type {
    public static function get_key(): string { return 'rdct-text-segment'; }
    protected function define_shape(): array {
        $shape = array();
        foreach ( \RadmanCTComponents\Components\TextReveal\segment_fields() as $key => $field ) {
            if ( ! empty( $field['image'] ) ) { $shape[ $key ] = \Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type::make(); continue; }
            if ( ! empty( $field['size'] ) ) { $shape[ $key ] = \Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type::make()->units( $field['units'] )->default_unit( $field['default_unit'] ?? 'px' ); continue; }
            $prop = is_int( $field['default'] ) ? \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::make() : String_Prop_Type::make();
            if ( ! empty( $field['float'] ) ) { $prop->float(); }
            if ( isset( $field['options'] ) ) { $prop->enum( array_map( 'strval', array_keys( $field['options'] ) ) ); }
            $shape[ $key ] = $prop->default( $field['default'] );
        }
        return $shape;
    }
}
class TextSegments extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type {
    public static function get_key(): string { return 'rdct-text-segments'; }
    protected function define_item_type(): \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type { return TextSegment::make(); }
    protected function validate_value( $value ): bool { return is_array( $value ) && count( $value ) <= 30 && parent::validate_value( $value ); }
}
class TextSegmentsControl extends \Elementor\Modules\AtomicWidgets\Controls\Base\Atomic_Control_Base {
    public function get_type(): string { return 'rdct-text-segments'; }
    public function get_props(): array { return array(); }
}
