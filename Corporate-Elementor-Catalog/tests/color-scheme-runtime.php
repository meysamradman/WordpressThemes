<?php

require getenv( 'RDCT_WP_ROOT' ) . '/wp-load.php';
function scheme_expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }
$defaults = \RadmanCT\Settings\defaults();
$off = \RadmanCT\Settings\normalize( array( 'color_scheme' => 'off' ), $defaults );
$filter = static function ( $pre, $option ) use ( &$off ) { return 'radman_settings' === $option ? $off : $pre; };
add_filter( 'pre_option', $filter, 10, 2 );
scheme_expect( '' === \RadmanCT\ColorScheme\toggle(), 'Disabled feature still renders a toggle.' );
$preview = \RadmanCT\ColorScheme\toggle( array(), true );
scheme_expect( str_contains( $preview, 'data-rdct-theme-toggle' ) && str_contains( $preview, '--rdct-toggle-radius:0px' ) && str_contains( $preview, '--rdct-toggle-border-width:0px' ), 'Builder preview is hidden or has an imposed frame.' );
\RadmanCT\ColorScheme\enqueue();
scheme_expect( ! wp_style_is( 'rdct-color-scheme', 'enqueued' ), 'Disabled feature loaded scheme CSS.' );
$off['color_scheme'] = 'system';
scheme_expect( \RadmanCT\ColorScheme\enabled(), 'Enabled mode did not resolve.' );
\RadmanCT\ColorScheme\enqueue();
scheme_expect( wp_style_is( 'rdct-color-scheme', 'enqueued' ), 'Enabled feature did not load its own CSS.' );
ob_start(); \RadmanCT\ColorScheme\bootstrap(); $bootstrap = ob_get_clean();
scheme_expect( str_contains( $bootstrap, 'localStorage' ) && str_contains( $bootstrap, 'rdctSchemeMode' ), 'Early preference initialization is missing.' );
$html = do_shortcode( '[rdct_theme_toggle]' );
scheme_expect( str_contains( $html, 'data-rdct-theme-toggle' ) && str_contains( $html, 'aria-pressed' ) && 2 === substr_count( $html, '<svg' ), 'PHP/Elementor shortcode toggle failed.' );
$custom = do_shortcode( '[rdct_theme_toggle light_icon="layout-grid" dark_icon="custom-menu-offset" style="outline" radius="8" light_color="#112233" dark_color="#eeeeee" hover_color="#ffcc00" icon_size="32" padding="4"]' );
scheme_expect( str_contains( $custom, '--rdct-toggle-radius:8px' ) && str_contains( $custom, '--rdct-toggle-light-color:#112233' ) && str_contains( $custom, '--rdct-toggle-dark-color:#eeeeee' ) && str_contains( $custom, '--rdct-toggle-hover-color:#ffcc00' ) && str_contains( $custom, 'data-appearance="outline"' ) && str_contains( $custom, 'M7 6h15' ), 'Shared icon or style options did not reach shortcode output.' );
scheme_expect( ! str_contains( $custom, '--rdct-text-h2-color' ) && ! str_contains( $custom, '--cursor-' ), 'Toggle colors leaked into typography or cursor variables.' );
$text_only = \RadmanCT\ColorScheme\toggle( array( 'light_icon' => '', 'dark_icon' => '', 'label' => 'Switch scheme' ) );
scheme_expect( ! str_contains( $text_only, '<svg' ) && str_contains( $text_only, '>Switch scheme</span>' ), 'Removing both icons left an empty button.' );
scheme_expect( null === \RadmanCT\ColorScheme\toggle_options( array( 'radius' => -1 ) ) && null === \RadmanCT\ColorScheme\toggle_options( array( 'hover_color' => 'red;display:none' ) ), 'Invalid toggle appearance passed validation.' );
$invalid = \RadmanCT\Settings\normalize( array( 'color_scheme' => 'invalid', 'dark_text' => 'red;display:none', 'cursor_hover_effect' => 'invalid', 'cursor_follow_duration' => -1 ), $defaults );
scheme_expect( $invalid['color_scheme'] === $defaults['color_scheme'] && $invalid['dark_text'] === $defaults['dark_text'] && $invalid['cursor_follow_duration'] === $defaults['cursor_follow_duration'], 'Invalid settings were accepted.' );
require_once get_template_directory() . '/inc/builder/schema.php';
require_once get_template_directory() . '/inc/builder/rendering.php';
$ids = array(); $items = \RadmanCT\Builder\validate_items( array( array( 'id' => 'scheme-toggle-test', 'type' => 'theme-toggle', 'schemeOptions' => array( 'light_icon' => 'layout-grid', 'radius' => 9, 'light_color' => '#112233' ) ) ), $ids );
scheme_expect( null !== $items && str_contains( \RadmanCT\Builder\Rendering\item( 'header', $items[0], 'default', false ), 'data-rdct-theme-toggle' ), 'Builder toggle failed.' );
scheme_expect( 'layout-grid' === $items[0]['schemeOptions']['light_icon'] && 9.0 === $items[0]['schemeOptions']['radius'], 'Builder storage validation discarded the element options.' );
foreach ( array( 'custom-menu-offset', 'layout-grid', 'sun', 'moon' ) as $name ) { scheme_expect( str_contains( \RadmanCT\Icons\render( $name ), 'stroke-width="1"' ), 'Selected icon missing or wrong stroke.' ); }
remove_filter( 'pre_option', $filter, 10 );
echo "Passed: scheme settings validation, disabled output, early preference, shortcode/PHP/builder toggle and selected SVGs; no site settings saved.\n";
