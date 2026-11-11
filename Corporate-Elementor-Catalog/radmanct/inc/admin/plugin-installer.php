<?php

namespace RadmanCT\Admin\PluginInstaller;

defined( 'ABSPATH' ) || exit;

function admin_url( string $path ): string {
	return is_multisite() && current_user_can( 'manage_network_plugins' ) ? network_admin_url( $path ) : \admin_url( $path );
}

require_once __DIR__ . '/plugins/packages.php';

function information( $result, string $action, $args ) {
	$id = '';
	$bundle = null;
	foreach ( bundles() as $key => $candidate ) {
		if ( is_object( $args ) && $candidate['slug'] === ( $args->slug ?? '' ) ) { $id = $key; $bundle = $candidate; break; }
	}
	if ( 'plugin_information' !== $action || ! $bundle ) {
		return $result;
	}
	$installing = 'update.php' === ( $GLOBALS['pagenow'] ?? '' ) && 'install-plugin' === ( $_GET['action'] ?? '' );
	$nonce = $_REQUEST['_wpnonce'] ?? null;
	if ( $installing ? ( ! current_user_can( 'install_plugins' ) || ! is_string( $nonce ) || ! wp_verify_nonce( $nonce, 'install-plugin_' . $bundle['slug'] ) ) : ( ! current_user_can( 'update_plugins' ) && ! current_user_can( 'edit_theme_options' ) ) ) {
		return new \WP_Error( 'radman_install_denied', __( 'You are not allowed to install this plugin.', 'radmanct' ) );
	}
	$package = $installing ? bundle_package( $id ) : bundle_metadata( $id );
	if ( ! $package ) {
		return new \WP_Error( 'radman_package_missing', __( 'The local plugin package is missing or invalid. Upload the plugin ZIP through WordPress instead.', 'radmanct' ) );
	}
	return (object) array( 'name' => $bundle['name'], 'slug' => $bundle['slug'], 'version' => $package['version'], 'download_link' => $installing ? $package['path'] : '', 'requires' => $bundle['requires'], 'requires_php' => '8.0', 'external' => true, 'sections' => array( 'description' => ! empty( $bundle['required'] ) ? __( 'Required shared components for custom PHP and optional Elementor pages.', 'radmanct' ) : __( 'Optional Radman CT extension.', 'radmanct' ) ) );
}

function action( string $id, array $plugin ): ?array {
	if ( isset( bundles()[ $id ] ) && $plugin['installed'] && current_user_can( 'update_plugins' ) ) {
		$package = bundle_metadata( $id );
		if ( $package && version_compare( $package['version'], $plugin['version'] ?? '', '>' ) ) {
			$url = add_query_arg( array( 'action' => 'upgrade-plugin', 'plugin' => $plugin['file'] ), admin_url( 'update.php' ) );
			return array( 'url' => wp_nonce_url( $url, 'upgrade-plugin_' . $plugin['file'] ), 'label' => __( 'Update', 'radmanct' ), 'icon' => 'update' );
		}
	}
	if ( ! isset( bundles()[ $id ] ) && ! empty( $plugin['update_available'] ) && current_user_can( 'update_plugins' ) ) {
		$url = add_query_arg( array( 'action' => 'upgrade-plugin', 'plugin' => $plugin['file'] ), admin_url( 'update.php' ) );
		return array( 'url' => wp_nonce_url( $url, 'upgrade-plugin_' . $plugin['file'] ), 'label' => __( 'Update', 'radmanct' ), 'icon' => 'update' );
	}
	if ( $plugin['active'] ) {
		return null;
	}
	if ( $plugin['installed'] ) {
		if ( $plugin['dependency_missing'] || ! current_user_can( 'activate_plugin', $plugin['file'] ) ) {
			return null;
		}
		$url = add_query_arg( array( 'action' => 'activate', 'plugin' => $plugin['file'] ), \admin_url( 'plugins.php' ) );
		return array( 'url' => wp_nonce_url( $url, 'activate-plugin_' . $plugin['file'] ), 'label' => __( 'Activate', 'radmanct' ), 'icon' => 'controls-play' );
	}
	if ( ! current_user_can( 'install_plugins' ) ) {
		return null;
	}
	if ( isset( bundles()[ $id ] ) && bundle_metadata( $id ) ) {
		$slug = bundles()[ $id ]['slug'];
		$url = add_query_arg( array( 'action' => 'install-plugin', 'plugin' => $slug ), admin_url( 'update.php' ) );
		return array( 'url' => wp_nonce_url( $url, 'install-plugin_' . $slug ), 'label' => __( 'Install', 'radmanct' ), 'icon' => 'download' );
	}
	return current_user_can( 'upload_plugins' ) ? array( 'url' => admin_url( 'plugin-install.php?tab=upload' ), 'label' => __( 'Upload ZIP', 'radmanct' ), 'icon' => 'upload' ) : null;
}

add_filter( 'plugins_api', __NAMESPACE__ . '\\information', 10, 3 );
