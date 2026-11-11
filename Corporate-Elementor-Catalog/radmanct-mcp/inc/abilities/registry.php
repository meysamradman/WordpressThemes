<?php

namespace RadmanCT\Mcp;

defined( 'ABSPATH' ) || exit;

function object_schema( array $properties, array $required = array() ): array {
	$schema = array( 'type' => 'object', 'properties' => $properties, 'required' => $required, 'additionalProperties' => false );
	if ( ! $required ) { $schema['default'] = new \stdClass(); }
	return $schema;
}

function context(): array {
	$result = array( 'site' => home_url( '/' ), 'environment' => wp_get_environment_type(), 'locale' => get_locale(), 'content_types' => Content\types(), 'writes_enabled' => permission( true ), 'elementor_abilities' => array(), 'layouts' => array() );
	$result['elementor_abilities'] = Integrations\Elementor\abilities();
	$result['boundaries'] = array(
		'Elementor ability names indicate registration, not verified MCP exposure or support for every document. Use Elementor native tools for its content.',
		'Custom PHP source files, theme settings, layout assignments, translations and shared blocks cannot be changed through these Radman abilities.',
		'Read the target and current version before updates. Use save-layout dry_run before saving. Publication must be explicitly requested.',
	);
	if ( Integrations\Theme\available() ) { foreach ( array( 'header', 'footer' ) as $kind ) { $result['layouts'][ $kind ] = \RadmanCT\Builder\Storage\listing( $kind ); } }
	return $result;
}

function register(): void {
	$id = array( 'type' => 'integer', 'minimum' => 1 );
	$page = array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 10000 );
	$type = array( 'type' => 'string', 'enum' => Content\types() );
	$search = array( 'type' => 'string', 'maxLength' => 80 );
	$fields = array( 'title' => array( 'type' => 'string', 'maxLength' => 200 ), 'content' => array( 'type' => 'string', 'maxLength' => 262144 ), 'excerpt' => array( 'type' => 'string', 'maxLength' => 8192 ), 'slug' => array( 'type' => 'string', 'maxLength' => 200 ), 'status' => array( 'type' => 'string', 'enum' => array( 'draft', 'pending', 'publish', 'private' ) ), 'featured_media' => array( 'type' => 'integer', 'minimum' => 0 ), 'template' => array( 'type' => 'string', 'maxLength' => 200 ) );
	foreach ( Content\types() as $name ) {
		foreach ( Content\taxonomies( $name ) as $taxonomy => $tax ) { $fields[ $tax->rest_base ?: $taxonomy ] = array( 'type' => 'array', 'items' => $id, 'maxItems' => 40, 'uniqueItems' => true ); }
	}
	$write = array( 'fields' => object_schema( $fields ), 'details' => array( 'type' => 'object', 'description' => 'Complete Core schema version 1 from read-content. Only for active products or portfolio.' ) );
	$layout = array( 'kind' => array( 'type' => 'string', 'enum' => array( 'header', 'footer' ) ), 'layout_id' => array( 'type' => 'string', 'pattern' => '^(default|l[a-f0-9]{32})$' ) );
	$tools = array(
		'site-context' => array( false, 'Read site identity, available content types, named PHP layouts and official Elementor abilities. Never returns credentials.', object_schema( array() ), static function () { return context(); } ),
		'list-content' => array( false, 'List editable WordPress pages, blog posts, active products or portfolio. Bounded pagination with total_pages.', object_schema( array( 'type' => $type, 'page' => $page, 'search' => $search, 'per_page' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 20 ) ), array( 'type' ) ), __NAMESPACE__ . '\\Content\\listing' ),
		'list-media' => array( false, 'Find existing Media Library images and their attachment IDs, URLs, alt text and dimensions. Bounded pagination. No uploads, downloads or modifications.', object_schema( array( 'page' => $page, 'search' => $search, 'per_page' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 20 ) ) ), __NAMESPACE__ . '\\Content\\media' ),
		'read-content' => array( false, 'Read editable content, Core details, taxonomy IDs and a version required for updates. Elementor structure belongs to official Elementor tools.', object_schema( array( 'id' => $id ), array( 'id' ) ), static function ( $input ) { return Content\snapshot( $input['id'] ); } ),
		'list-terms' => array( false, 'Find existing category, brand, feature or tag IDs for the chosen content type. Taxonomy is its WordPress name. Returns total_pages.', object_schema( array( 'type' => $type, 'taxonomy' => array( 'type' => 'string', 'maxLength' => 64 ), 'page' => $page, 'search' => $search ), array( 'type', 'taxonomy' ) ), __NAMESPACE__ . '\\Content\\terms' ),
		'create-content' => array( true, 'Create native content; draft by default. Publication requires explicit status and native publishing permission. No PHP files or Elementor JSON.', object_schema( $write + array( 'type' => $type ), array( 'type', 'fields' ) ), __NAMESPACE__ . '\\Content\\save' ),
		'update-content' => array( true, 'Update only specified fields using the version returned by read-content. Published content stays published unless status changes. No deletion. Elementor body changes require its official tools.', object_schema( $write + array( 'id' => $id, 'version' => array( 'type' => 'string', 'pattern' => '^[a-f0-9]{64}$' ) ), array( 'id', 'version', 'fields' ) ), __NAMESPACE__ . '\\Content\\save' ),
	);
	if ( function_exists( '\\RadmanCT\\Builder\\read' ) ) {
		$tools['read-layout'] = array( false, 'Read a Radman CT PHP header/footer layout and revision. This is independent of Elementor.', object_schema( $layout, array( 'kind' ) ), __NAMESPACE__ . '\\Integrations\Theme\\read' );
		$tools['save-layout'] = array( true, 'Validate or save an existing Radman CT PHP header/footer layout through its native validator and atomic revision check. Can affect all pages using this layout. Does not change assignments. Use dry_run to validate first.', object_schema( $layout + array( 'layout' => array( 'type' => 'object' ), 'revision' => array( 'type' => 'integer', 'minimum' => 0 ), 'dry_run' => array( 'type' => 'boolean' ) ), array( 'kind', 'layout', 'revision' ) ), __NAMESPACE__ . '\\Integrations\Theme\\save' );
	}
	foreach ( $tools as $name => $tool ) {
		if ( $tool[0] && ! permission_setting() ) { continue; }
		wp_register_ability( 'radmanct/' . $name, array( 'label' => 'Radman CT: ' . $name, 'description' => $tool[1], 'category' => 'radmanct', 'input_schema' => $tool[2], 'output_schema' => array( 'type' => 'object' ), 'permission_callback' => static function () use ( $tool ): bool { return permission( $tool[0] ); }, 'execute_callback' => static function ( $input ) use ( $tool ) { return permission( $tool[0] ) ? call_user_func( $tool[3], $input ) : Content\error( 'MCP access is disabled or not permitted.', 'radmanct_mcp_forbidden' ); }, 'meta' => array( 'public' => false, 'show_in_rest' => false, 'mcp' => array( 'public' => true ), 'annotations' => array( 'readonly' => ! $tool[0], 'destructive' => $tool[0], 'idempotent' => ! $tool[0] ) ) ) );
	}
}

function permission_setting(): bool {
	return '1' === (string) get_option( 'radmanct_mcp_write', '0' );
}
