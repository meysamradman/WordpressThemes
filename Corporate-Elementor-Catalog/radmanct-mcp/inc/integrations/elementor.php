<?php

namespace RadmanCT\Mcp\Integrations\Elementor;

defined( 'ABSPATH' ) || exit;

function owns( int $id ): bool {
	return 'builder' === get_post_meta( $id, '_elementor_edit_mode', true );
}

function abilities(): array {
	$result = array();
	foreach ( wp_get_abilities() as $ability ) {
		if ( in_array( $ability->get_name(), array( 'elementor/get-structure', 'elementor/create-page', 'elementor/build-composition', 'elementor/manage-elements', 'elementor/update-settings', 'elementor/publish-document' ), true ) ) { $result[] = $ability->get_name(); }
	}
	return $result;
}
