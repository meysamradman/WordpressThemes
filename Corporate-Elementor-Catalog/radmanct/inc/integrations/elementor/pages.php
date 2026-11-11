<?php

namespace RadmanCT\Integrations\Elementor\Pages;

defined( 'ABSPATH' ) || exit;

function content_data( $data, int $post_id ) {
	if ( ! is_admin() && is_page() && $post_id === get_queried_object_id() && \RadmanCT\Layout\custom_template( $post_id ) ) {
		if ( current_user_can( 'edit_post', $post_id ) && \RadmanCT\Modules\editor_preview() ) {
			return $data;
		}
		return array();
	}
	return $data;
}

add_filter( 'elementor/frontend/builder_content_data', __NAMESPACE__ . '\\content_data', 20, 2 );
