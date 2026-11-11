<?php

namespace RadmanCT\Admin\SettingsPage;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/settings/labels.php';
require_once __DIR__ . '/settings/assets.php';
require_once __DIR__ . '/settings/save.php';

function register(): void {
	register_setting( 'radman_settings', 'radman_settings', array( 'type' => 'object', 'default' => \RadmanCT\Settings\defaults(), 'sanitize_callback' => '\\RadmanCT\\Settings\\sanitize', 'show_in_rest' => false ) );
}

function menu(): void {
	add_menu_page( __( 'Radman CT settings', 'radmanct' ), __( 'Radman CT', 'radmanct' ), 'edit_theme_options', 'rdct-settings', __NAMESPACE__ . '\\render', 'dashicons-admin-customizer', 61 );
}

function render(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Radman CT settings', 'radmanct' ); ?></h1>
		<?php settings_errors(); ?>
		<form class="rdct-stg-form" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post" novalidate>
			<?php settings_fields( 'radman_settings' ); ?>
			<div id="rdct-settings-root"><p class="rdct-loading" role="status"><?php esc_html_e( 'Loading...', 'radmanct' ); ?></p></div>
			<noscript><p><?php esc_html_e( 'Enable JavaScript to edit these settings.', 'radmanct' ); ?></p></noscript>
		</form>
	</div>
	<?php
}

function register_cursor_labels( $previous, $values ): void {
	if ( ! is_array( $values ) ) {
		return;
	}
	foreach ( array( 'image', 'carousel' ) as $kind ) {
		$value = $values[ 'cursor_' . $kind . '_label' ] ?? '';
		if ( is_string( $value ) ) {
			do_action( 'wpml_register_single_string', 'Radman Cursor', $kind . '-label', $value );
		}
	}
}

add_action( 'admin_init', __NAMESPACE__ . '\\register' );
add_action( 'admin_menu', __NAMESPACE__ . '\\menu' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue' );
add_filter( 'wp_script_attributes', __NAMESPACE__ . '\\script_attributes' );
add_action( 'update_option_radman_settings', __NAMESPACE__ . '\\register_cursor_labels', 10, 2 );
add_action( 'add_option_radman_settings', __NAMESPACE__ . '\\register_cursor_labels', 10, 2 );
add_filter( 'option_page_capability_radman_settings', static function (): string { return 'edit_theme_options'; } );
