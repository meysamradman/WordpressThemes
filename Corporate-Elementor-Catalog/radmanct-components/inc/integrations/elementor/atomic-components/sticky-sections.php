<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/sticky-sections.php';

class StickySectionItem extends CurtainItem {
	public static function get_key(): string { return 'rdct-sticky-section-item'; }
	protected static function item_fields(): array { return \RadmanCTComponents\Components\StickySections\item_fields(); }
}

class StickySectionItems extends CurtainItems {
	public static function get_key(): string { return 'rdct-sticky-section-items'; }
	protected function define_item_type(): \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type { return StickySectionItem::make(); }
}

class StickySectionItemsControl extends CurtainItemsControl {
	public function get_type(): string { return 'rdct-sticky-section-items'; }
}

class StickySections extends Component {
	protected const MODE = '';
	public function show_in_panel() { return '' !== static::MODE; }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public static function get_element_type(): string { return static::MODE ? 'rdct-atomic-sticky-' . static::MODE : 'rdct-atomic-sticky-sections'; }
	protected static function component(): string { return 'sticky-sections'; }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'sticky-sections', $this->get_atomic_settings() ); }
	public function get_title() { return ( static::MODE ? \RadmanCTComponents\Components\StickySections\fields()['mode']['options'][ static::MODE ] : __( 'Sticky Sections', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-slider-vertical'; }
	public static function typed_items( array $items ): array {
		$shape = StickySectionItem::make()->get_shape(); $typed = array();
		foreach ( array_slice( $items, 0, 12 ) as $row ) {
			$fields = array();
			foreach ( $shape as $key => $prop ) { if ( array_key_exists( $key, $row ) ) { $fields[ $key ] = typed_field( $prop, $row[ $key ] ); } }
			$typed[] = StickySectionItem::generate( $fields );
		}
		return $typed;
	}
	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		if ( static::MODE ) { $schema['mode'] = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::make()->enum( array( static::MODE ) )->default( static::MODE ); }
		$schema['items'] = StickySectionItems::make()->default( static::typed_items( array( array( 'title' => __( 'First item', 'radmanct-components' ) ), array( 'title' => __( 'Second item', 'radmanct-components' ) ), array( 'title' => __( 'Third item', 'radmanct-components' ) ) ) ) );
		return $schema;
	}
	protected function define_atomic_controls(): array {
		$sections = parent::define_atomic_controls();
		$controls = array();
		foreach ( $sections[0]->get_items() as $control ) {
			$key = $control->get_bind();
			if ( 'mode' === $key && static::MODE ) { continue; }
			$field = \RadmanCTComponents\Components\StickySections\fields()[ $key ] ?? array();
			if ( static::MODE && isset( $field['condition']['mode'] ) && ! in_array( static::MODE, $field['condition']['mode'], true ) ) { continue; }
			if ( 'aside_template_id' === $key ) {
				require_once dirname( __DIR__, 3 ) . '/components/template-library.php';
				$options = array(); foreach ( \RadmanCTComponents\Components\TemplateLibrary\options() as $id => $title ) { $options[] = array( 'value' => (int) $id, 'label' => $title ); }
				$control = TemplateSelectControl::bind_to( $key )->set_options( $options )->set_label( __( 'Select template', 'radmanct-components' ) );
			}
			$controls[] = $control;
		}
		$sections[0]->set_items( $controls );
		$sections[0]->set_items( array_merge( array( StickySectionItemsControl::bind_to( 'items' )->set_label( __( 'Items', 'radmanct-components' ) ) ), $sections[0]->get_items() ) );
		return $sections;
	}
}

class StickyStack extends StickySections { protected const MODE = 'stack'; }
class StickyOverlap extends StickySections { protected const MODE = 'overlap'; }
class ServicePanels extends StickySections { protected const MODE = 'services'; }
class ZoomCards extends StickySections { protected const MODE = 'zoom'; }
class StickySplit extends StickySections { protected const MODE = 'split'; }
