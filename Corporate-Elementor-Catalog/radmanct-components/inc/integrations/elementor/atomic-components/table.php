<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/table.php';

class TableStrings extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type {
	public static function get_key(): string { return 'rdct-table-strings'; }
	protected function define_item_type(): \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type { return \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::make(); }
	protected function validate_value( $value ): bool { return is_array( $value ) && count( $value ) <= 12 && parent::validate_value( $value ); }
}
class TableRow extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type {
	public static function get_key(): string { return 'rdct-table-row'; }
	protected function define_shape(): array { return array( 'cells' => TableStrings::make() ); }
}
class TableRows extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type {
	public static function get_key(): string { return 'rdct-table-rows'; }
	protected function define_item_type(): \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type { return TableRow::make(); }
	protected function validate_value( $value ): bool { return is_array( $value ) && count( $value ) <= 100 && parent::validate_value( $value ); }
}
class TableData extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type {
	public static function get_key(): string { return 'rdct-table-data'; }
	protected function define_shape(): array { return array( 'columns' => TableStrings::make(), 'rows' => TableRows::make() ); }
}
class TableControl extends \Elementor\Modules\AtomicWidgets\Controls\Base\Atomic_Control_Base {
	public function get_type(): string { return 'rdct-table-grid'; }
	public function get_props(): array { return array(); }
}
class Table extends Component {
	public static function get_element_type(): string { return 'rdct-atomic-table'; }
	protected static function component(): string { return 'table'; }
	public function get_title() { return ( __( 'Table', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-table'; }
	public function get_script_depends() { return array(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'table', $this->get_atomic_settings() ); }
	public static function typed_data( array $data ): array {
		$strings = static fn( $cells ) => TableStrings::generate( array_map( static fn( $cell ) => \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( $cell ), $cells ) );
		return array( 'columns' => $strings( $data['columns'] ), 'rows' => TableRows::generate( array_map( static fn( $row ) => TableRow::generate( array( 'cells' => $strings( $row['cells'] ) ) ), $data['rows'] ) ) );
	}
	protected static function define_props_schema(): array { $schema = parent::define_props_schema(); $schema['data'] = TableData::make()->default( static::typed_data( \RadmanCTComponents\Components\Table\sample() ) ); return $schema; }
	protected function define_atomic_controls(): array {
		$content = array( TableControl::bind_to( 'data' )->set_label( __( 'Table content', 'radmanct-components' ) ) ); $style = array();
		foreach ( parent::define_atomic_controls()[0]->get_items() as $control ) { if ( in_array( $control->get_bind(), array( 'caption', 'header', 'row_headers' ), true ) ) { $content[] = $control; } else { $style[] = $control; } }
		return array( \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_table_content' )->set_label( __( 'Table content', 'radmanct-components' ) )->set_items( $content ), \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_table_style' )->set_label( __( 'Style', 'radmanct-components' ) )->set_items( $style ) );
	}
}
