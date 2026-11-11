<?php

namespace RadmanCTComponents\Integrations\Elementor\Table;

defined( 'ABSPATH' ) || exit;

class GridControl extends \Elementor\Base_Data_Control {
	public function get_type(): string { return 'rdct-table-grid'; }
	public function get_default_value() { return array( 'columns' => array( '', '' ), 'rows' => array( array( 'cells' => array( '', '' ) ) ) ); }
	public function content_template(): void { echo '<div data-rdct-table-editor></div>'; }
}
