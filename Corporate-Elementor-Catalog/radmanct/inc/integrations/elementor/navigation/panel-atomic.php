<?php

namespace RadmanCT\Integrations\Elementor\Navigation;

defined( 'ABSPATH' ) || exit;

class PanelAtomic extends Atomic {
	public static function get_element_type(): string { return 'rdct-atomic-panel'; }
	public function get_title() { return ( __( 'Off-canvas panel', 'radmanct' ) ); }
	public function get_icon() { return 'eicon-sidebar'; }
	protected static function control_fields(): array { return panel_fields(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : panel_styles( $this->get_atomic_settings() ); }
	protected function get_templates(): array { return array( 'radmanct/atomic/panel' => __DIR__ . '/panel.html.twig' ); }
	protected function render_navigation_settings( array $settings ): void { render_panel( $settings, $this->get_id() ); }
}
