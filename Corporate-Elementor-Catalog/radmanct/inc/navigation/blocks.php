<?php

namespace RadmanCT\Navigation\Blocks;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__ ) . '/content-blocks.php';

function render( int $id, string $prefix ): string {
	$html = \RadmanCT\ContentBlocks\render( $id, $prefix );
	$processor = new \WP_HTML_Tag_Processor( $html );
	while ( $processor->next_tag( 'IMG' ) ) {
		$processor->set_attribute( 'loading', 'lazy' );
		$processor->set_attribute( 'decoding', 'async' );
		$processor->remove_attribute( 'fetchpriority' );
	}
	return $processor->get_updated_html();
}
