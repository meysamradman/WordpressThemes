<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/timeline.php';

class TimelineItem extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type {
	public static function get_key(): string { return 'rdct-timeline-item'; }
	protected static function item_fields(): array { return \RadmanCTComponents\Components\Timeline\item_fields(); }
	protected function define_shape(): array {
		$shape = array();
		foreach ( static::item_fields() as $key => $field ) {
			if ( ! empty( $field['media'] ) ) { $shape[ $key ] = image_field_prop(); continue; }
			if ( ! empty( $field['rich'] ) ) { $shape[ $key ] = rich_text_prop( $field['default'] ); continue; }
			if ( ! empty( $field['template'] ) ) { $shape[ $key ] = template_field_prop(); continue; }
			$shape[ $key ] = ( is_int( $field['default'] ) ? Number_Prop_Type::make() : String_Prop_Type::make() )->default( $field['default'] );
			if ( isset( $field['options'] ) ) { $shape[ $key ]->enum( array_keys( $field['options'] ) ); }
		}
		return $shape;
	}
}

class TimelineItems extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type {
	public static function get_key(): string { return 'rdct-timeline-items'; }
	protected function define_item_type(): \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type { return TimelineItem::make(); }
	protected function validate_value( $value ): bool { return is_array( $value ) && count( $value ) <= 100 && parent::validate_value( $value ); }
}

class TimelineItemsControl extends \Elementor\Modules\AtomicWidgets\Controls\Base\Atomic_Control_Base {
	public function get_type(): string { return 'rdct-timeline-items'; }
	public function get_props(): array { return array(); }
}

class Timeline extends Component {
	public static function get_element_type(): string { return 'rdct-atomic-timeline'; }
	protected static function component(): string { return 'timeline'; }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'timeline', $this->get_atomic_settings() ); }
	public function get_title() { return ( __( 'Timeline', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-time-line'; }
	public static function typed_items( array $items ): array {
		$shape = TimelineItem::make()->get_shape(); $typed = array();
		foreach ( array_slice( $items, 0, 100 ) as $row ) {
			$fields = array();
			foreach ( $shape as $key => $prop ) {
				if ( ! array_key_exists( $key, $row ) ) { continue; }
				$fields[ $key ] = typed_field( $prop, $row[ $key ] );
			}
			$typed[] = TimelineItem::generate( $fields );
		}
		return $typed;
	}
	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		$schema['items'] = TimelineItems::make()->default( static::typed_items( array( array( 'title' => __( 'First item', 'radmanct-components' ) ), array( 'title' => __( 'Second item', 'radmanct-components' ) ), array( 'title' => __( 'Third item', 'radmanct-components' ) ) ) ) );
		return $schema;
	}
	protected function define_atomic_controls(): array {
		$sections = parent::define_atomic_controls();
		$content = array( TimelineItemsControl::bind_to( 'items' )->set_label( __( 'Items', 'radmanct-components' ) ) ); $style = array();
		foreach ( $sections[0]->get_items() as $control ) { if ( str_ends_with( $control->get_bind(), '_responsive' ) ) { $style[] = $control; } else { $content[] = $control; } }
		$sections[0]->set_items( $content );
		if ( $style ) { $sections[] = \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_timeline_style' )->set_label( __( 'Style', 'radmanct-components' ) )->set_items( $style ); }
		return $sections;
	}
}
