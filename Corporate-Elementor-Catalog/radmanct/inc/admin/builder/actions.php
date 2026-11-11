<?php

namespace RadmanCT\Admin\BuilderPage;

defined( 'ABSPATH' ) || exit;

function delete_layout(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to edit this layout.', 'radmanct' ), '', array( 'response' => 403 ) );
	}
	$values = 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) ? $_POST : $_GET;
	$kind = $values['kind'] ?? '';
	$id = $values['id'] ?? '';
	if ( ! in_array( $kind, array( 'header', 'footer' ), true ) || ! is_string( $id ) || ! \RadmanCT\Builder\Storage\valid_id( $id ) || ! \RadmanCT\Builder\Storage\exists( $kind, $id ) ) {
		wp_die( esc_html__( 'Invalid request.', 'radmanct' ), '', array( 'response' => 400 ) );
	}
	check_admin_referer( 'radman_delete_layout_' . $kind . '_' . $id );
	$list = admin_url( 'admin.php?page=rdct-' . $kind );
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
		wp_safe_redirect( $list );
		exit;
	}
	$error = \RadmanCT\Admin\BuilderApi\delete_layout( $kind, $id );
	if ( null !== $error ) { wp_die( esc_html( $error ), '', array( 'response' => 409, 'back_link' => true ) ); }
	wp_safe_redirect( $list );
	exit;
}

add_action( 'admin_post_radman_delete_layout', __NAMESPACE__ . '\\delete_layout' );

function rename_layout(): void {
    if ( ! current_user_can( 'edit_theme_options' ) ) { wp_die( esc_html__( 'You are not allowed to edit this layout.', 'radmanct' ), '', array( 'response' => 403 ) ); }
    $kind = $_REQUEST['kind'] ?? '';
    $id = $_REQUEST['id'] ?? '';
    if ( ! in_array( $kind, array( 'header', 'footer' ), true ) || ! is_string( $id ) || ! \RadmanCT\Builder\Storage\valid_id( $id ) || ! \RadmanCT\Builder\Storage\exists( $kind, $id ) ) { wp_die( esc_html__( 'Invalid request.', 'radmanct' ), '', array( 'response' => 400 ) ); }
    wp_safe_redirect( add_query_arg( array( 'page' => 'rdct-' . $kind, 'layout' => $id, 'rename' => 1 ), admin_url( 'admin.php' ) ) );
    exit;
}

add_action( 'admin_post_radman_rename_layout', __NAMESPACE__ . '\\rename_layout' );

function save(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to edit this layout.', 'radmanct' ), '', array( 'response' => 403 ) );
	}
	$kind = $_POST['kind'] ?? '';
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || ! in_array( $kind, array( 'header', 'footer' ), true ) ) {
		wp_die( esc_html__( 'Invalid request.', 'radmanct' ), '', array( 'response' => 400 ) );
	}
	check_admin_referer( 'radman_save_builder_' . $kind );
	$id = $_POST['id'] ?? 'default';
	if ( ! \RadmanCT\Builder\Storage\valid_id( $id ) || ! \RadmanCT\Builder\Storage\exists( $kind, $id ) ) {
		wp_die( esc_html__( 'Invalid request.', 'radmanct' ), '', array( 'response' => 400 ) );
	}
	$raw = $_POST['layout'] ?? '';
	$revision = $_POST['revision'] ?? '';
	$valid_revision = is_string( $revision ) && preg_match( '/^[0-9]{1,10}$/', $revision );
	$layout = is_string( $raw ) && strlen( $raw ) <= 262144 ? \RadmanCT\Builder\validate( json_decode( wp_unslash( $raw ), true, 16 ) ) : null;
	$status = 'invalid';
	if ( $layout && $valid_revision ) {
		$status = \RadmanCT\Builder\Storage\save( $kind, $id, $layout, (int) $revision )['status'];
	}
	wp_safe_redirect( add_query_arg( array( 'page' => 'rdct-' . $kind, 'layout' => $id, 'radman_status' => $status ), admin_url( 'admin.php' ) ) );
	exit;
}
