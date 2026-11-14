<?php

namespace RadmanCT\Admin\RequirementsPage;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/plugin-installer.php';
require_once __DIR__ . '/plugin-updater.php';

function screen( ?string $value = null ): string {
	static $hook = '';
	if ( null !== $value ) { $hook = $value; }
	return $hook;
}

function menu(): void {
	screen( add_submenu_page( 'rdct-settings', __( 'Plugins and requirements', 'radmanct' ), __( 'Plugins and requirements', 'radmanct' ), 'edit_theme_options', 'rdct-requirements', __NAMESPACE__ . '\\render' ) );
}

function enqueue( string $hook ): void {
	if ( '' !== screen() && screen() === $hook && current_user_can( 'edit_theme_options' ) ) {
		\RadmanCT\Assets\style( 'rdct-admin-requirements', 'src/css/admin/requirements.css' );
	}
}

function registry(): array {
	return array(
		'components' => array( 'file' => 'radmanct-components/radmanct-components.php', 'name' => __( 'Radman Components', 'radmanct' ), 'required' => true, 'purpose' => __( 'Shared component library for Elementor pages.', 'radmanct' ) ),
		'content' => array( 'file' => 'radmanct-content/radmanct-content.php', 'name' => __( 'Radman Content', 'radmanct' ), 'purpose' => __( 'Required only for the optional product and portfolio modules.', 'radmanct' ) ),
		'mcp-adapter' => array( 'file' => 'mcp-adapter/mcp-adapter.php', 'name' => 'MCP Adapter', 'purpose' => __( 'Required only for an enabled MCP connection.', 'radmanct' ) ),
		'mcp' => array( 'file' => 'radmanct-mcp/radmanct-mcp.php', 'name' => __( 'Radman MCP', 'radmanct' ), 'purpose' => __( 'Optional MCP tools for content and Radman CT layouts.', 'radmanct' ), 'depends' => 'mcp-adapter' ),
		'elementor' => array( 'file' => 'elementor/elementor.php', 'name' => 'Elementor', 'required' => true, 'purpose' => __( 'Page builder for this Elementor edition.', 'radmanct' ) ),
	);
}

function statuses(): array {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return array();
	}
	if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$installed = get_plugins();
	$plugins = registry();
	$updates = get_site_transient( 'update_plugins' );
	foreach ( $plugins as &$plugin ) {
		$plugin['installed'] = isset( $installed[ $plugin['file'] ] );
		$plugin['active'] = $plugin['installed'] && is_plugin_active( $plugin['file'] );
		$plugin['version'] = $plugin['installed'] ? (string) ( $installed[ $plugin['file'] ]['Version'] ?? '' ) : '';
		$plugin['available'] = '';
		foreach ( \RadmanCT\Admin\PluginInstaller\bundles() as $id => $bundle ) {
			if ( $bundle['file'] === $plugin['file'] ) { $plugin['available'] = \RadmanCT\Admin\PluginInstaller\bundle_metadata( $id )['version'] ?? ''; }
		}
		$update = is_object( $updates ) ? ( $updates->response[ $plugin['file'] ] ?? null ) : null;
		if ( '' === $plugin['available'] && is_object( $update ) ) { $plugin['available'] = (string) ( $update->new_version ?? '' ); }
		$plugin['update_available'] = $plugin['installed'] && '' !== $plugin['available'] && version_compare( $plugin['available'], $plugin['version'], '>' );
	}
	unset( $plugin );
	foreach ( $plugins as &$plugin ) {
		$plugin['dependency_missing'] = isset( $plugin['depends'] ) && ! $plugins[ $plugin['depends'] ]['active'];
	}
	unset( $plugin );
	return $plugins;
}

function render(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$plugins = statuses();
	echo '<div class="wrap"><h1>' . esc_html__( 'Plugins and requirements', 'radmanct' ) . '</h1>';
	echo '<p>' . esc_html__( 'This edition uses Elementor and Radman Components. Elementor Pro provides content single/archive templates; the Radman header/footer builder remains independent.', 'radmanct' ) . '</p>';
	
	if ( current_user_can( 'activate_plugins' ) || current_user_can( 'manage_network_plugins' ) ) {
		$url = is_multisite() && current_user_can( 'manage_network_plugins' ) ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' );
		echo '<p><a class="button" href="' . esc_url( $url ) . '">' . esc_html__( 'Open plugin manager', 'radmanct' ) . '</a></p>';
	}
	$icons = array( 'components' => 'screenoptions', 'content' => 'screenoptions', 'mcp' => 'admin-generic', 'mcp-adapter' => 'admin-plugins', 'elementor' => 'layout' );
	echo '<div class="rdct-plugins">';
	foreach ( $plugins as $id => $plugin ) {
		$status = ! $plugin['installed'] ? __( 'Not installed', 'radmanct' ) : ( $plugin['active'] ? __( 'Active', 'radmanct' ) : __( 'Inactive', 'radmanct' ) );
		echo '<section class="rdct-plugin"><div class="rdct-plugin__heading"><span class="dashicons dashicons-' . esc_attr( $icons[ $id ] ) . '" aria-hidden="true"></span><div><h2>' . esc_html( $plugin['name'] ) . '</h2><span>' . esc_html( ! empty( $plugin['required'] ) ? __( 'Required', 'radmanct' ) : __( 'Optional', 'radmanct' ) ) . '</span></div></div>';
		echo '<div class="rdct-plugin__status"><span class="rdct-plugin__badge' . ( $plugin['active'] ? ' is-active' : '' ) . '">' . esc_html( $status ) . '</span><bdi dir="ltr">' . esc_html( $plugin['version'] ) . '</bdi></div><p>' . esc_html( $plugin['purpose'] ) . '</p>';
		if ( '' !== $plugin['available'] ) {
			echo '<p>' . esc_html__( 'Available version:', 'radmanct' ) . ' <bdi dir="ltr">' . esc_html( $plugin['available'] ) . '</bdi>' . ( $plugin['update_available'] ? ' <strong>' . esc_html__( 'Update available', 'radmanct' ) . '</strong>' : '' ) . '</p>';
		}
		if ( isset( $plugin['depends'] ) ) {
			echo '<p>' . esc_html__( 'Prerequisite:', 'radmanct' ) . ' ' . esc_html( $plugins[ $plugin['depends'] ]['name'] );
			if ( $plugin['dependency_missing'] ) {
				echo ' <strong>' . esc_html__( 'The prerequisite is not active.', 'radmanct' ) . '</strong>';
			}
			echo '</p>';
		}
		$action = \RadmanCT\Admin\PluginInstaller\action( $id, $plugin );
		if ( isset( \RadmanCT\Admin\PluginInstaller\bundles()[ $id ] ) && ! \RadmanCT\Admin\PluginInstaller\bundle_package( $id ) ) {
			echo '<p class="description">' . esc_html__( 'The local plugin package is missing or invalid. Upload the plugin ZIP through WordPress instead.', 'radmanct' ) . '</p>';
		}
		echo '<div class="rdct-plugin__actions">';
		if ( $action ) {
			echo '<a class="button button-primary" href="' . esc_url( $action['url'] ) . '"><span class="dashicons dashicons-' . esc_attr( $action['icon'] ) . '" aria-hidden="true"></span>' . esc_html( $action['label'] ) . '</a>';
		}
		echo '</div>';

		echo '</section>';
	}
	echo '</div>';
	if ( $plugins['content']['active'] && function_exists( '\\RadmanCTContent\\Modules\\enabled' ) && current_user_can( 'manage_options' ) ) {
		echo '<p><a class="button" href="' . esc_url( admin_url( 'admin.php?page=radmanct-content' ) ) . '">' . esc_html__( 'Manage product and portfolio modules', 'radmanct' ) . '</a></p>';
	}
	echo '<p>' . esc_html__( 'Built-in theme features do not need separate plugins. Enable effects in theme settings; enable products and portfolio independently in Radman Content.', 'radmanct' ) . '</p></div>';
}

add_action( 'admin_menu', __NAMESPACE__ . '\\menu', 30 );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue' );

add_action( 'admin_notices', static function (): void {
	if ( ! current_user_can( 'edit_theme_options' ) || defined( 'RDCT_COMPONENTS_FILE' ) ) { return; }
	echo '<div class="notice notice-warning"><p>' . esc_html__( 'Radman Components is required for this Elementor edition.', 'radmanct' ) . ' <a href="' . esc_url( \admin_url( 'admin.php?page=rdct-requirements' ) ) . '">' . esc_html__( 'Manage required plugins', 'radmanct' ) . '</a></p></div>';
} );

add_action( 'admin_notices', static function (): void {
	if ( ! current_user_can( 'edit_theme_options' ) || did_action( 'elementor/loaded' ) ) { return; }
	echo '<div class="notice notice-warning"><p>' . esc_html__( 'Elementor is required to design pages in this edition.', 'radmanct' ) . ' <a href="' . esc_url( admin_url( 'admin.php?page=rdct-requirements' ) ) . '">' . esc_html__( 'Manage required plugins', 'radmanct' ) . '</a></p></div>';
} );
