<?php

namespace RadmanCT\Integrations\Wpml\Builder;

defined( 'ABSPATH' ) || exit;

function languages(): array {
	static $cached = null;
	if ( null !== $cached ) { return $cached; }
	global $sitepress;
	$active = is_object( $sitepress ) && is_callable( array( $sitepress, 'get_active_languages' ) ) ? $sitepress->get_active_languages() : apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	$result = array();
	foreach ( is_array( $active ) ? $active : array() as $language ) {
		if ( ! is_array( $language ) ) { continue; }
		$code = $language['code'] ?? null;
		if ( ! is_string( $code ) || ! preg_match( '/^[a-zA-Z][a-zA-Z0-9_-]{0,19}$/D', $code ) ) { continue; }
		$title = $language['native_name'] ?? $language['translated_name'] ?? $code;
		$result[ $code ] = is_string( $title ) ? sanitize_text_field( $title ) : $code;
	}
	$cached = $result;
	return $cached;
}

function members( int $trid ): array {
	$result = $trid ? apply_filters( 'wpml_get_element_translations', null, $trid, ELEMENT_TYPE ) : array();
	return is_array( $result ) ? $result : array();
}

function translation_state( string $kind, string $id ): ?array {
	if ( ! enabled() ) { return null; }
	$active = languages();
	if ( count( $active ) < 2 ) { return null; }
	$post_id = reference( $kind, $id ); $info = $post_id ? details( $post_id ) : null;
	$source = $info->language_code ?? apply_filters( 'wpml_default_language', null );
	$source = isset( $active[ $source ] ) ? $source : array_key_first( $active );
	$translations = $info ? members( (int) $info->trid ) : array();
	$items = array();
	foreach ( $active as $code => $title ) {
		$translated = $translations[ $code ]->element_id ?? 0;
		$layout = $translated ? layout_id( $kind, (int) $translated ) : null;
		$items[] = array( 'code' => $code, 'title' => $title, 'id' => $layout );
	}
	return array( 'source' => $source, 'bound' => (bool) $info, 'direction' => true === apply_filters( 'wpml_is_rtl', $source ) ? 'rtl' : 'ltr', 'items' => $items );
}

function render_preview( string $kind, string $id, callable $render ): string {
	$post_id = reference( $kind, $id ); $info = $post_id ? details( $post_id ) : null;
	$language = $info->language_code ?? null;
	$previous = apply_filters( 'wpml_current_language', null );
	if ( ! $language || $language === $previous ) { return $render(); }
	do_action( 'wpml_switch_language', $language );
	try { return $render(); }
	finally { do_action( 'wpml_switch_language', $previous ); }
}

function binding( string $kind, string $id, string $language, int $trid = 0, ?string $source = null, int $reuse = 0 ) {
	$reference = reference( $kind, $id );
	if ( $reference ) {
		$info = details( $reference );
		return $info && $info->language_code === $language && ( ! $trid || (int) $info->trid === $trid ) ? $reference : new \WP_Error( 'rdct_wpml_bound', __( 'This layout is already connected to another translation.', 'radmanct' ) );
	}
	$title = $id;
	foreach ( \RadmanCT\Builder\Storage\listing( $kind ) as $entry ) { if ( $entry['id'] === $id ) { $title = $entry['title']; break; } }
	$old_id = '';
	if ( $reuse ) {
		$old_id = get_post_meta( $reuse, '_rdct_builder_layout', true );
		if ( POST_TYPE !== get_post_type( $reuse ) || 'publish' !== get_post_status( $reuse ) || $kind !== get_post_meta( $reuse, '_rdct_builder_kind', true ) || ! \RadmanCT\Builder\Storage\valid_id( $old_id ) || \RadmanCT\Builder\Storage\exists( $kind, $old_id ) ) { return new \WP_Error( 'rdct_wpml_bound', __( 'This layout is already connected to another translation.', 'radmanct' ) ); }
		$post_id = $reuse;
	} else {
		$post_id = wp_insert_post( array( 'post_type' => POST_TYPE, 'post_status' => 'publish', 'post_title' => wp_slash( $title ), 'post_author' => get_current_user_id() ), true );
		if ( is_wp_error( $post_id ) || ! $post_id ) { return new \WP_Error( 'rdct_wpml_write', __( 'The translation connection could not be saved.', 'radmanct' ) ); }
	}
	update_post_meta( $post_id, '_rdct_builder_kind', $kind );
	update_post_meta( $post_id, '_rdct_builder_layout', $id );
	$old = get_option( INDEX, null ); $next = is_array( $old ) ? $old : array();
	if ( $old_id ) { unset( $next[ $kind ][ $old_id ] ); }
	$next[ $kind ][ $id ] = (int) $post_id;
	if ( ! \RadmanCT\Builder\Storage\compare_exchange( INDEX, $old, $next ) ) {
		if ( $reuse ) { update_post_meta( $post_id, '_rdct_builder_layout', $old_id ); }
		else { wp_delete_post( $post_id, true ); }
		return new \WP_Error( 'rdct_wpml_conflict', __( 'The translation library changed. Reload and try again.', 'radmanct' ) );
	}
	if ( reference( $kind, $id ) !== (int) $post_id ) {
		forget( $kind, $id );
		if ( $reuse ) { update_post_meta( $post_id, '_rdct_builder_layout', $old_id ); }
		else { wp_delete_post( $post_id, true ); }
		return new \WP_Error( 'rdct_wpml_write', __( 'The translation connection could not be saved.', 'radmanct' ) );
	}
	$initial = details( (int) $post_id );
	do_action( 'wpml_set_element_language_details', array( 'element_id' => (int) $post_id, 'element_type' => ELEMENT_TYPE, 'trid' => $trid ?: ( $initial->trid ?? false ), 'language_code' => $language, 'source_language_code' => $source ) );
	$info = details( (int) $post_id );
	if ( ! $info || $info->language_code !== $language || ( $trid && (int) $info->trid !== $trid ) ) { return new \WP_Error( 'rdct_wpml_write', __( 'The translation connection could not be saved.', 'radmanct' ) ); }
	return (int) $post_id;
}

function translate_layout( string $kind, string $id, string $source, string $target, int $revision, string $existing = '' ) {
	$active = languages();
	if ( ! enabled() || ! current_user_can( 'edit_theme_options' ) || ! in_array( $kind, array( 'header', 'footer' ), true ) || ! isset( $active[ $source ], $active[ $target ] ) || $source === $target || ! \RadmanCT\Builder\Storage\exists( $kind, $id ) ) { return new \WP_Error( 'rdct_wpml_invalid', __( 'Choose two active, different languages.', 'radmanct' ) ); }
	$lock = 'radman_builder_wpml_lock_' . md5( $kind . '/' . $id );
	$previous = get_option( $lock, null );
	$token = array( 'time' => time(), 'token' => wp_generate_uuid4() );
	if ( ( is_array( $previous ) && ( $previous['time'] ?? 0 ) > time() - 90 ) || ! \RadmanCT\Builder\Storage\compare_exchange( $lock, $previous, $token ) ) { return new \WP_Error( 'rdct_wpml_busy', __( 'This translation is being created. Try again shortly.', 'radmanct' ) ); }
	try {
		$stored = \RadmanCT\Builder\read( $kind, $id );
		if ( $stored['revision'] !== $revision ) { return new \WP_Error( 'rdct_wpml_conflict', __( 'This layout changed in another tab. Reload before saving again.', 'radmanct' ) ); }
		$source_id = binding( $kind, $id, $source );
		if ( is_wp_error( $source_id ) ) { return $source_id; }
		$info = details( $source_id );
		$translations = members( (int) $info->trid );
		$target_id = (int) ( $translations[ $target ]->element_id ?? 0 );
		$translated = $target_id ? layout_id( $kind, $target_id ) : null;
		if ( $translated ) { return $translated; }
		if ( $existing && ( $existing === $id || ! \RadmanCT\Builder\Storage\exists( $kind, $existing ) ) ) { return new \WP_Error( 'rdct_wpml_invalid', __( 'Choose an available layout of the same builder.', 'radmanct' ) ); }
		$new_id = $existing;
		if ( ! $new_id ) {
			$layout = $stored['layout']; $layout['enabled'] = false;
			$title = '';
			foreach ( \RadmanCT\Builder\Storage\listing( $kind ) as $entry ) { if ( $entry['id'] === $id ) { $title = $entry['title']; break; } }
			$new_id = \RadmanCT\Builder\Storage\create( $kind, wp_html_excerpt( $title . ' (' . $active[ $target ] . ')', 80, '' ), $layout );
			if ( ! $new_id ) { return new \WP_Error( 'rdct_wpml_limit', __( 'Could not create the layout. The limit is 12 saved layouts per builder.', 'radmanct' ) ); }
		}
		$result = binding( $kind, $new_id, $target, (int) $info->trid, ( $info->source_language_code ?? null ) ?: $info->language_code, $target_id );
		if ( is_wp_error( $result ) ) {
			if ( ! $existing ) { \RadmanCT\Builder\Storage\remove( $kind, $new_id ); }
			return $result;
		}
		\RadmanCT\Builder\Storage\register_strings( $kind, $new_id, \RadmanCT\Builder\read( $kind, $new_id )['layout'] );
		return $new_id;
	} finally { \RadmanCT\Builder\Storage\compare_exchange( $lock, $token, array( 'time' => 0 ) ); }
}

function forget( string $kind, string $id ): void {
	$old = get_option( INDEX, null );
	if ( ! is_array( $old ) || ! isset( $old[ $kind ][ $id ] ) ) { return; }
	$next = $old; unset( $next[ $kind ][ $id ] );
	\RadmanCT\Builder\Storage\compare_exchange( INDEX, $old, $next );
}

function dashboard_types( array $types ): array { unset( $types[ POST_TYPE ] ); return $types; }

function edit_reference( $url, int $post_id, string $context ) {
	if ( ! current_user_can( 'edit_theme_options' ) || POST_TYPE !== get_post_type( $post_id ) ) { return $url; }
	$kind = get_post_meta( $post_id, '_rdct_builder_kind', true );
	if ( ! in_array( $kind, array( 'header', 'footer' ), true ) ) { return $url; }
	$id = layout_id( $kind, $post_id );
	if ( ! $id ) { return $url; }
	$link = add_query_arg( array( 'page' => 'header' === $kind ? 'rdct-header' : 'rdct-footer', 'layout' => $id ), admin_url( 'admin.php' ) );
	return 'display' === $context ? esc_url( $link ) : $link;
}

add_action( 'radmanct/builder/deleted', __NAMESPACE__ . '\\forget', 10, 2 );
add_filter( 'wpml_tm_dashboard_translatable_types', __NAMESPACE__ . '\\dashboard_types' );
add_filter( 'get_edit_post_link', __NAMESPACE__ . '\\edit_reference', 10, 3 );
