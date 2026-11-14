<?php

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/inc/legacy-settings.php';

foreach ( array( 'settings', 'renderers', 'layout', 'page-effects', 'setup', 'assets', 'typography', 'template-tags', 'navigation', 'modules', 'builder', 'components', 'template-functions' ) as $rdct_module ) {
	require_once __DIR__ . '/inc/' . $rdct_module . '.php';
}
unset( $rdct_module );
\RadmanCT\ColorScheme\register();
require_once __DIR__ . '/inc/blog-media.php';
require_once __DIR__ . '/inc/card-media.php';
require_once __DIR__ . '/inc/sidebars.php';
require_once __DIR__ . '/inc/content-colors.php';
require_once __DIR__ . '/inc/page-title.php';
require_once __DIR__ . '/inc/integrations/components.php';
require_once __DIR__ . '/inc/media-svg.php';
if ( defined( 'WP_ROCKET_VERSION' ) ) { require_once __DIR__ . '/inc/integrations/rocket.php'; }
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) { require_once __DIR__ . '/inc/integrations/wpml.php'; }
if ( \RadmanCT\Settings\values()['block_google_fonts'] ) {
	require_once __DIR__ . '/inc/google-fonts.php';
}

if ( ! is_admin() && wp_doing_cron() ) {
	require_once __DIR__ . '/inc/admin/plugin-installer.php';
	require_once __DIR__ . '/inc/admin/plugin-updater.php';
}

add_action( 'after_setup_theme', static function (): void {
	if ( did_action( 'elementor/loaded' ) ) {
		require_once __DIR__ . '/inc/integrations/elementor.php';
	}
	if ( function_exists( '\\RadmanCTContent\\Modules\\types' ) && \RadmanCTContent\Modules\types() ) {
		require_once __DIR__ . '/inc/integrations/content.php';
	}
}, 20 );

if ( is_admin() ) {
	require_once __DIR__ . '/inc/admin/blog-media.php';
	require_once __DIR__ . '/inc/admin/card-media.php';
	require_once __DIR__ . '/inc/admin/navigation-items.php';

	require_once __DIR__ . '/inc/admin/typography.php';
	require_once __DIR__ . '/inc/admin/requirements-page.php';
	require_once __DIR__ . '/inc/admin/settings-page.php';
	require_once __DIR__ . '/inc/admin/builder-page.php';
	require_once __DIR__ . '/inc/admin/builder-api.php';
	require_once __DIR__ . '/inc/admin/builder-assignment.php';
	require_once __DIR__ . '/inc/admin/page-effects.php';
	require_once __DIR__ . '/inc/admin/page-title.php';
}
