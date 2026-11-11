<?php

namespace RadmanCTShared\Icons\Elementor;

defined( 'ABSPATH' ) || exit;

class Icon_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base {
	private \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base $original;
	public function __construct( \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base $original ) { $this->original = $original; }
	public function transform( $value, \Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context $context ) {
		if ( ! is_array( $value ) || 'rdct-local' !== ( $value['library'] ?? '' ) ) { return $this->original->transform( $value, $context ); }
		$html = render_icon( $value );
		if ( $html && class_exists( '\\WP_HTML_Tag_Processor' ) ) {
			$processor = new \WP_HTML_Tag_Processor( $html );
			if ( $processor->next_tag( array( 'tag_name' => 'SVG' ) ) ) { $processor->set_attribute( 'style', 'width:100%;height:100%;overflow:unset;' . $processor->get_attribute( 'style' ) ); $html = $processor->get_updated_html(); }
		}
		return array( 'html' => $html, 'url' => null );
	}
}
