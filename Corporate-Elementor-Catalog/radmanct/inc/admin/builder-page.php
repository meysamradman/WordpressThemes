<?php

namespace RadmanCT\Admin\BuilderPage;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/builder/labels.php';
require_once __DIR__ . '/builder/assets.php';
require_once __DIR__ . '/builder/actions.php';

function menu(): void {
	screens( array(
		add_submenu_page( 'rdct-settings', __( 'Header builder', 'radmanct' ), __( 'Header builder', 'radmanct' ), 'edit_theme_options', 'rdct-header', __NAMESPACE__ . '\\render' ),
		add_submenu_page( 'rdct-settings', __( 'Footer builder', 'radmanct' ), __( 'Footer builder', 'radmanct' ), 'edit_theme_options', 'rdct-footer', __NAMESPACE__ . '\\render' ),
	) );

}

function screens( ?array $value = null ): array {
	static $hooks = array();
	if ( null !== $value ) {
		$hooks = $value;
	}
	return $hooks;
}

function kind(): string {
	return 'rdct-footer' === ( $_GET['page'] ?? '' ) ? 'footer' : 'header';
}

function selected_id(): string {
	if ( creating() ) { return 'default'; }
	$id = $_GET['layout'] ?? 'default';
	return \RadmanCT\Builder\Storage\valid_id( $id ) && \RadmanCT\Builder\Storage\exists( kind(), $id ) ? $id : 'default';
}

function editing(): bool {
	return creating() || ( isset( $_GET['layout'] ) && is_string( $_GET['layout'] ) && '' !== $_GET['layout'] );
}

function creating(): bool { return '1' === ( $_GET['new'] ?? null ); }

function copy_source(): string {
	$id = $_GET['copy'] ?? '';
	return creating() && is_string( $id ) && \RadmanCT\Builder\Storage\valid_id( $id ) && \RadmanCT\Builder\Storage\exists( kind(), $id ) ? $id : '';
}

function draft_data(): array {
	$source = copy_source();
	return array( 'layout' => '' !== $source ? \RadmanCT\Builder\read( kind(), $source )['layout'] : \RadmanCT\Builder\validate( array( 'version' => 1, 'enabled' => false, 'rows' => array() ) ), 'revision' => 0 );
}

function render(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	if ( ! editing() ) {
		require_once __DIR__ . '/builder/library.php';
		render_library();
		return;
	}
	$kind = kind();
	echo '<div class="wrap"><h1>' . esc_html( labels()[ $kind ] ) . '</h1>';
	$status = $_GET['radman_status'] ?? '';
	$messages = array(
		'saved' => __( 'Layout saved.', 'radmanct' ),
		'conflict' => __( 'This layout changed in another tab. Reload before saving again.', 'radmanct' ),
		'invalid' => __( 'The layout is invalid. Your saved layout was preserved.', 'radmanct' ),
	);
	if ( is_string( $status ) && isset( $messages[ $status ] ) ) {
		echo '<div class="notice ' . ( 'saved' === $status ? 'notice-success' : 'notice-error' ) . '"><p>' . esc_html( $messages[ $status ] ) . '</p></div>';
	}
	echo '<form id="rdct-builder-form" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" method="post">';
	wp_nonce_field( 'radman_save_builder_' . $kind );
	echo '<input type="hidden" name="action" value="radman_save_builder"><input type="hidden" name="kind" value="' . esc_attr( $kind ) . '"><div id="rdct-builder-root"><p class="rdct-loading" role="status">' . esc_html__( 'Loading...', 'radmanct' ) . '</p></div><noscript><p>' . esc_html__( 'Enable JavaScript to edit these settings.', 'radmanct' ) . '</p></noscript></form></div>';
}

add_action( 'admin_menu', __NAMESPACE__ . '\\menu', 20 );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue' );
add_action( 'admin_post_radman_save_builder', __NAMESPACE__ . '\\save' );
add_filter( 'wp_script_attributes', __NAMESPACE__ . '\\script_attributes' );
