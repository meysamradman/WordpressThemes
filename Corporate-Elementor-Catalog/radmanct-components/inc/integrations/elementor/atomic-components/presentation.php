<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/presentation.php';

class ResponsiveValues extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type {
	public static function get_key(): string { return 'rdct-responsive'; }
	protected function define_shape(): array {
		$shape = array();
		foreach ( array( 'desktop', 'laptop', 'tablet', 'mobile' ) as $device ) { $shape[ $device ] = \Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type::make()->add_prop_type( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::make()->float() )->add_prop_type( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::make() )->add_prop_type( \Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type::make()->units( array( 'px', '%', 'em', 'rem', 'vw', 'vh', 'lh', 'rlh' ) )->default_unit( 'px' ) ); }
		return $shape;
	}
}

class ResponsiveControl extends \Elementor\Modules\AtomicWidgets\Controls\Base\Atomic_Control_Base {
	private string $control_type = '';
	public function configure( string $type, string $key ): self { $this->control_type = 'rdct-responsive-' . $type . '-' . str_replace( '_', '-', $key ); return $this; }
	public function get_type(): string { return $this->control_type; }
	public function get_props(): array { return array(); }
}

class LocalIconControl extends \Elementor\Modules\AtomicWidgets\Controls\Base\Atomic_Control_Base {
	public function get_type(): string { return 'rdct-icon-picker'; }
	public function get_props(): array { return array(); }
}
