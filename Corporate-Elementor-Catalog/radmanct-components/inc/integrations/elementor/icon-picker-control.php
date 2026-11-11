<?php

namespace RadmanCTComponents\Integrations\Elementor\IconPicker;

defined( 'ABSPATH' ) || exit;

class IconControl extends \Elementor\Control_Icons {
	public function get_type(): string { return 'rdct-icon-picker'; }
	public function get_default_value() { return ''; }
	public function get_value( $control, $settings ) {
		$value = $settings[ $control['name'] ] ?? $control['default'] ?? '';
		return is_string( $value ) ? $value : '';
	}
	protected function get_default_settings() {
		$settings = parent::get_default_settings();
		$settings['skin'] = 'inline';
		$settings['exclude_inline_options'] = array( 'svg' );
		$settings['dynamic'] = array( 'active' => false );
		$settings['recommended'] = array( 'rdct-local' => array_keys( \RadmanCTComponents\Icons\catalog() ) );
		$settings['description'] = __( 'Choose an icon from Radman CT. Project icons are registered in the shared local catalog.', 'radmanct-components' );
		return $settings;
	}
}
