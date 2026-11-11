<?php

namespace RadmanCT\Admin\BuilderApi;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/builder/assignments.php';

function fail( string $message, int $status = 400 ): void {
	wp_send_json_error( array( 'message' => $message ), $status );
}

function payload(): ?array {
	$raw = $_POST['layout'] ?? '';
	return is_string( $raw ) && strlen( $raw ) <= 262144 ? \RadmanCT\Builder\validate( json_decode( wp_unslash( $raw ), true, 16 ) ) : null;
}

function title(): ?string {
	$raw = $_POST['title'] ?? null;
	if ( ! is_string( $raw ) || strlen( $raw ) > 320 ) {
		return null;
	}
	$value = sanitize_text_field( wp_unslash( $raw ) );
	$length = preg_match_all( '/./us', $value );
	return false !== $length && $length > 0 && $length <= 80 ? $value : null;
}

function defaults(): array {
	$value = get_theme_mod( 'radman_builder_defaults', array() );
	return is_array( $value ) ? $value : array();
}

function state( string $kind, string $id ): array {
	return array( 'id' => $id, 'data' => \RadmanCT\Builder\read( $kind, 'none' === $id ? 'default' : $id ), 'layouts' => \RadmanCT\Builder\Storage\listing( $kind ), 'defaults' => defaults()[ $kind ] ?? array(), 'translation' => function_exists( '\\RadmanCT\\Integrations\\Wpml\\Builder\\translation_state' ) ? \RadmanCT\Integrations\Wpml\Builder\translation_state( $kind, $id ) : null );
}

function delete_layout( string $kind, string $id ): ?string {
	if ( ! \RadmanCT\Builder\Storage\remove( $kind, $id ) ) { return __( 'This layout is no longer available.', 'radmanct' ); }
	$values = defaults();
	foreach ( $values[ $kind ] ?? array() as $language => $selected ) {
		if ( $selected === $id ) { $values[ $kind ][ $language ] = 'none'; }
	}
	if ( 'default' === $id && ! isset( $values[ $kind ]['all'] ) ) { $values[ $kind ]['all'] = 'none'; }
	set_theme_mod( 'radman_builder_defaults', $values );
	delete_metadata( 'post', 0, '_radman_' . $kind . '_layout', $id, true );
	do_action( 'radmanct/builder/assigned', $kind, null, 'all' );
	return null;
}

function request(): void {
	nocache_headers();
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		fail( __( 'You are not allowed to edit this layout.', 'radmanct' ), 403 );
	}
	$nonce = $_REQUEST['nonce'] ?? null;
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || ! is_string( $nonce ) || '' === $nonce || strlen( $nonce ) > 128 || ! check_ajax_referer( 'radman_builder', 'nonce', false ) ) {
		fail( __( 'Your session expired. Reload the editor before trying again.', 'radmanct' ), 403 );
	}
	$kind = $_POST['kind'] ?? '';
	$id = $_POST['id'] ?? 'default';
	$operation = $_POST['operation'] ?? '';
	if ( ! in_array( $operation, array( 'assignments', 'read', 'inherit', 'save', 'preview', 'validate', 'create', 'default', 'delete', 'rename', 'translate' ), true ) ) {
		fail( __( 'Invalid request.', 'radmanct' ) );
	}
	$virtual = in_array( $operation, array( 'assignments', 'inherit', 'create', 'preview', 'validate' ), true ) && 'default' === $id;
	$hidden = 'default' === $operation && 'none' === $id;
	if ( ! in_array( $kind, array( 'header', 'footer' ), true ) || ( ! $hidden && ! \RadmanCT\Builder\Storage\valid_id( $id ) ) || ( ! $virtual && ! $hidden && ! \RadmanCT\Builder\Storage\exists( $kind, $id ) ) ) {
		fail( __( 'This layout is no longer available.', 'radmanct' ), 404 );
	}
	if ( 'assignments' === $operation ) { wp_send_json_success( assignment_state( $kind ) ); }
	if ( 'read' === $operation ) {
		if ( isset( $_POST['language'] ) ) {
			$language = $_POST['language'];
			$languages = function_exists( '\\RadmanCT\\Integrations\\Wpml\\Builder\\languages' ) ? \RadmanCT\Integrations\Wpml\Builder\languages() : array();
			if ( ! is_string( $language ) || ( 'all' !== $language && ! isset( $languages[ $language ] ) ) ) { fail( __( 'Invalid request.', 'radmanct' ) ); }
			$id = defaults()[ $kind ][ $language ] ?? defaults()[ $kind ]['all'] ?? 'default';
			$id = is_string( $id ) && \RadmanCT\Builder\Storage\valid_id( $id ) ? $id : 'default';
			if ( function_exists( '\\RadmanCT\\Integrations\\Wpml\\Builder\\resolve_language' ) ) { $id = \RadmanCT\Integrations\Wpml\Builder\resolve_language( $id, $kind, $language ); }
			if ( ! \RadmanCT\Builder\Storage\exists( $kind, $id ) ) { $id = 'default'; }
		}
		wp_send_json_success( state( $kind, $id ) );
	}
	if ( 'translate' === $operation ) {
		$source = $_POST['source_language'] ?? null; $target = $_POST['language'] ?? null;
		$revision = $_POST['revision'] ?? null; $existing = $_POST['existing'] ?? '';
		if ( ! function_exists( '\\RadmanCT\\Integrations\\Wpml\\Builder\\translate_layout' ) || ! is_string( $source ) || ! is_string( $target ) || strlen( $source ) > 20 || strlen( $target ) > 20 || ! is_string( $revision ) || ! preg_match( '/^[0-9]{1,10}$/D', $revision ) || ! is_string( $existing ) || ( '' !== $existing && ! \RadmanCT\Builder\Storage\valid_id( $existing ) ) ) { fail( __( 'Invalid request.', 'radmanct' ) ); }
		$result = \RadmanCT\Integrations\Wpml\Builder\translate_layout( $kind, $id, $source, $target, (int) $revision, $existing );
		if ( is_wp_error( $result ) ) { fail( $result->get_error_message(), 409 ); }
		wp_send_json_success( state( $kind, $result ) );
	}
	if ( 'inherit' === $operation ) {
		$lang = $_POST['language'] ?? '';
		if ( ! assignment_language( $lang, true, $kind ) ) {
			fail( __( 'Invalid request.', 'radmanct' ) );
		}
		$values = defaults();
		unset( $values[ $kind ][ $lang ] );
		set_theme_mod( 'radman_builder_defaults', $values );
		do_action( 'radmanct/builder/assigned', $kind, null, $lang );
		wp_send_json_success( state( $kind, $id ) );
	}
	if ( in_array( $operation, array( 'save', 'preview', 'validate', 'create' ), true ) ) {
		$layout = payload();
		if ( ! $layout ) {
			fail( __( 'The layout is invalid. Your saved layout was preserved.', 'radmanct' ) );
		}
		if ( 'preview' === $operation ) {
			require_once __DIR__ . '/builder-preview.php';
			$direction = 'rtl' === ( $_POST['direction'] ?? '' ) ? 'rtl' : 'ltr';
			$render = static fn() => \RadmanCT\Admin\BuilderPreview\document( $kind, $layout, $direction );
			$html = function_exists( '\\RadmanCT\\Integrations\\Wpml\\Builder\\render_preview' ) ? \RadmanCT\Integrations\Wpml\Builder\render_preview( $kind, $id, $render ) : $render();
			wp_send_json_success( array( 'html' => $html ) );
		}
		if ( 'validate' === $operation ) {
			wp_send_json_success( array( 'layout' => $layout ) );
		}
		if ( 'create' === $operation ) {
			$title = title();
			if ( null === $title ) {
				fail( __( 'Enter a layout name with up to 80 characters.', 'radmanct' ) );
			}
			$new_id = \RadmanCT\Builder\Storage\create( $kind, $title, $layout );
			if ( ! $new_id ) {
				fail( __( 'Could not create the layout. The limit is 12 saved layouts per builder.', 'radmanct' ), 409 );
			}
			wp_send_json_success( state( $kind, $new_id ) );
		}
		$revision = $_POST['revision'] ?? '';
		$new_title = isset( $_POST['title'] ) ? title() : null;
		if ( isset( $_POST['title'] ) && null === $new_title ) { fail( __( 'Enter a layout name with up to 80 characters.', 'radmanct' ) ); }
		if ( ! is_string( $revision ) || ! preg_match( '/^[0-9]{1,10}$/D', $revision ) ) {
			fail( __( 'Invalid request.', 'radmanct' ) );
		}
		$result = \RadmanCT\Builder\Storage\save( $kind, $id, $layout, (int) $revision );
		if ( 'saved' !== $result['status'] ) {
			fail( __( 'This layout changed in another tab. Reload before saving again.', 'radmanct' ), 409 );
		}
		$renamed = null === $new_title || \RadmanCT\Builder\Storage\rename( $kind, $id, $new_title );
		$response = state( $kind, $id );
		if ( ! $renamed ) { $response['warning'] = __( 'The layout name could not be updated.', 'radmanct' ); }
		wp_send_json_success( $response );
	}
	if ( 'default' === $operation ) {
		$data = 'none' === $id ? null : \RadmanCT\Builder\read( $kind, $id );
		if ( 'none' !== $id && ! $data['layout']['enabled'] ) {
			fail( __( 'Save and enable the layout before assigning it.', 'radmanct' ) );
		}
		$lang = $_POST['language'] ?? 'all';
		if ( ! assignment_language( $lang ) ) {
			fail( __( 'Invalid request.', 'radmanct' ) );
		}
		$values = defaults();
		$values[ $kind ][ $lang ] = $id;
		set_theme_mod( 'radman_builder_defaults', $values );
		do_action( 'radmanct/builder/assigned', $kind, $id, $lang );
		wp_send_json_success( state( $kind, $id ) );
	}
	if ( 'delete' === $operation ) {
		$error = delete_layout( $kind, $id );
		if ( null !== $error ) { fail( $error, 409 ); }
		wp_send_json_success( state( $kind, 'default' ) );
	}
	if ( 'rename' === $operation ) {
		$title = title();
		if ( null === $title || ! \RadmanCT\Builder\Storage\rename( $kind, $id, $title ) ) {
			fail( __( 'The layout name could not be updated.', 'radmanct' ), 409 );
		}
		wp_send_json_success( state( $kind, $id ) );
	}
	fail( __( 'Invalid request.', 'radmanct' ) );
}

add_action( 'wp_ajax_radman_builder', __NAMESPACE__ . '\\request' );
