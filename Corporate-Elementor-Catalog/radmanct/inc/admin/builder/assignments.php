<?php

namespace RadmanCT\Admin\BuilderApi;

defined( 'ABSPATH' ) || exit;

function assignment_state( string $kind ): array {
	$layouts = \RadmanCT\Builder\Storage\listing( $kind );
	$multilingual = function_exists( '\\RadmanCT\\Integrations\\Wpml\\Builder\\enabled' ) && \RadmanCT\Integrations\Wpml\Builder\enabled();
	foreach ( $layouts as &$layout ) {
		$layout['enabled'] = \RadmanCT\Builder\read( $kind, $layout['id'] )['layout']['enabled'];
		$layout['language'] = $multilingual ? \RadmanCT\Integrations\Wpml\Builder\source_language( $kind, $layout['id'] ) : null;
	}
	unset( $layout );
	return array( 'layouts' => $layouts, 'defaults' => (object) ( defaults()[ $kind ] ?? array() ) );
}

function assignment_language( $language, bool $removing = false, string $kind = '' ): bool {
	if ( ! is_string( $language ) || ! preg_match( '/^[a-zA-Z][a-zA-Z0-9_-]{0,19}$/D', $language ) ) { return false; }
	if ( 'all' === $language ) { return ! $removing; }
	$languages = function_exists( '\\RadmanCT\\Integrations\\Wpml\\Builder\\languages' ) ? \RadmanCT\Integrations\Wpml\Builder\languages() : array();
	return isset( $languages[ $language ] ) || ( $removing && isset( defaults()[ $kind ][ $language ] ) );
}
