<?php

namespace RadmanCT\Integrations\Elementor\Navigation;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/classic.php';
require_once dirname( __DIR__, 3 ) . '/navigation/panel.php';

function panel_fields(): array {
	$fields = \RadmanCT\Navigation\Panel\fields();
	unset( $fields['panelCustomContent'], $fields['panelContentType']['options']['custom'] );
	$fields['panelContentType']['default'] = 'elementor';
	foreach ( $fields as &$field ) { $field['group'] ??= 'panel'; }
	unset( $field ); return $fields;
}

function panel_options( array $input ): ?array {
	$result = array();
	foreach ( panel_fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( is_array( $value ) && isset( $value['$$type'] ) ) { $value = ! empty( $value['disabled'] ) ? $field['default'] : ( $value['value'] ?? $field['default'] ); }
		if ( is_int( $field['default'] ) ) { if ( ! is_numeric( $value ) || ! is_finite( (float) $value ) || floor( (float) $value ) !== (float) $value ) { return null; } $value = (int) $value; }
		$result[ $key ] = $value;
	}
	if ( isset( $input['panelImage']['id'] ) ) { $result['panelImageId'] = (int) $input['panelImage']['id']; }
	return \RadmanCT\Navigation\Panel\settings( $result );
}

function panel_styles( array $input ): array {
	$options = panel_options( $input ); if ( null === $options ) { return array(); }
	$definitions = \RadmanCT\Components\definitions();
	return array_map( static fn( $name ) => $definitions[ $name ]['handle'], \RadmanCT\Navigation\Panel\asset_names( $options ) );
}

function render_panel( array $input, string $scope = '' ): void {
	$options = panel_options( $input );
	if ( null !== $options ) { \RadmanCT\Navigation\Panel\render( $options, null, $scope ); }
}

class PanelClassic extends Classic {
	public function get_name() { return 'rdct-panel'; }
	public function get_title() { return ( __( 'Off-canvas panel', 'radmanct' ) ); }
	public function get_icon() { return 'eicon-sidebar'; }
	public function show_in_panel() { return true; }
	protected static function control_fields(): array { return panel_fields(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : panel_styles( $this->get_settings_for_display() ); }
	protected function render() { render_panel( $this->get_settings_for_display(), $this->get_id() ); }
}
