<?php
defined( 'ABSPATH' ) || exit;
$rdct_web = $args['item'];
$rdct_type = \RadmanCT\Settings\values();
$rdct_brand = $rdct_type['brand_color'];
$rdct_web_index = (int) ( $args['index'] ?? 0 );
$rdct_web_icons = array(
	array( 'client-security', 'client-responsive', 'client-performance' ),
	array( 'client-performance', 'client-code', 'client-network' ),
	array( 'client-security', 'client-code', 'client-network' ),
	array( 'client-responsive', 'client-performance', 'client-code' ),
)[ $rdct_web_index ];
$rdct_web_url = \RadmanCTChild\Home\data( get_the_ID() )['button_url'] ?: '#';
?>
<p class="rdct-clt-web__eyebrow" lang="en"><bdi><?php echo esc_html( sprintf( '%02d', $rdct_web_index + 1 ) ); ?></bdi><?php echo esc_html( array( 'WEB DEVELOPMENT', 'WORDPRESS DEVELOPMENT', 'WEB SYSTEMS', 'APP DEVELOPMENT' )[ $rdct_web_index ] ); ?></p>
<h2 class="rdct-clt-web__title" data-rdct-panel-title><?php echo esc_html( $rdct_web[1] ); ?> <br><em><?php echo esc_html( $rdct_web[2] ); ?></em></h2>
<p class="rdct-clt-web__description" data-rdct-panel-copy><?php echo esc_html( $rdct_web[3] ); ?></p>
<ul class="rdct-clt-web__features">
	<?php foreach ( $rdct_web[4] as $rdct_web_index => $rdct_web_feature ) : ?><li><?php rdct_icon( $rdct_web_icons[ $rdct_web_index ] ); ?><?php echo esc_html( $rdct_web_feature ); ?></li><?php endforeach; ?>
</ul>
<?php if ( function_exists( 'rdct_button' ) ) : ?>
<div class="rdct-clt-web__cta"><?php
	rdct_button( array(
		'label' => __( 'More information', 'radmanct-child' ), 'url' => $rdct_web_url, 'tag' => 'a',
		'style' => 'plain', 'size' => 'custom', 'custom_height' => 72, 'padding_x' => 0, 'padding_y' => 8,
		'font_size' => 17, 'color' => $rdct_type['link_color'] ?: $rdct_brand, 'hover_color' => $rdct_type['link_hover_color'] ?: $rdct_brand,
		'icon' => 'arrow-right', 'icon_position' => 'end', 'icon_size' => 22, 'gap' => 22,
		'icon_style' => 'solid', 'icon_shape' => 'circle', 'icon_box_size' => 54,
		'icon_background' => $rdct_brand, 'icon_color' => '#fffaf0', 'icon_border_color' => $rdct_brand,
		'hover_icon_background' => $rdct_brand, 'hover_icon_color' => '#ffffff', 'hover_icon_border_color' => $rdct_brand,
	) );
?></div>
<?php endif; ?>
