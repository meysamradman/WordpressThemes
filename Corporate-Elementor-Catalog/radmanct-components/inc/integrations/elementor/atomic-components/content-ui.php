<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;

defined( 'ABSPATH' ) || exit;

class TaxonomyList extends Component {
	public static function get_element_type(): string { return 'rdct-atomic-taxonomy-list'; }
	protected static function component(): string { return 'taxonomy-list'; }
	public function get_title() { return __( 'Taxonomy list', 'radmanct-components' ); }
	public function get_icon() { return 'eicon-post-list'; }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'taxonomy-list', $this->get_atomic_settings() ); }
	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		$schema['taxonomy'] = String_Prop_Type::make()->default( 'category' );
		return $schema;
	}
}

class ArchiveSearch extends Component {
	public static function get_element_type(): string { return 'rdct-atomic-archive-search'; }
	protected static function component(): string { return 'archive-search'; }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'archive-search', $this->get_atomic_settings() ); }
	public function get_title() { return __( 'Content search', 'radmanct-components' ); }
	public function get_icon() { return 'eicon-search'; }
	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		$schema['post_type'] = String_Prop_Type::make()->default( 'post' );
		return $schema;
	}
}
