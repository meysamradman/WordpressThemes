<?php
defined( 'ABSPATH' ) || exit;
$rdct_web = $args['item'];
$rdct_web_index = (int) ( $args['index'] ?? 0 );
$rdct_web_icons = array(
	array( 'client-security', 'client-responsive', 'client-performance' ),
	array( 'client-performance', 'client-code', 'client-network' ),
	array( 'client-security', 'client-code', 'client-network' ),
	array( 'client-responsive', 'client-performance', 'client-code' ),
)[ $rdct_web_index ];
$rdct_web_url = \RadmanCT\Home\data( get_the_ID() )['button_url'] ?: '#';
?>
<p class="rdc-svc__eb" lang="en"><bdi><?php echo esc_html( sprintf( '%02d', $rdct_web_index + 1 ) ); ?></bdi><?php echo esc_html( array( 'WEB DEVELOPMENT', 'WORDPRESS DEVELOPMENT', 'WEB SYSTEMS', 'APP DEVELOPMENT' )[ $rdct_web_index ] ); ?></p>
<h2 class="rdc-svc__ttl rdc-dsp-ttl" data-rdc-pnl-ttl><?php echo esc_html( $rdct_web[1] ); ?> <br><em><?php echo esc_html( $rdct_web[2] ); ?></em></h2>
<p class="rdc-svc__desc" data-rdc-pnl-copy><?php echo esc_html( $rdct_web[3] ); ?></p>
<ul class="rdc-svc__fts" data-rdc-pnl-en>
	<?php foreach ( $rdct_web[4] as $rdct_web_index => $rdct_web_feature ) : ?><li><?php echo \RadmanCT\icon( $rdct_web_icons[ $rdct_web_index ] ); ?><?php echo esc_html( $rdct_web_feature ); ?></li><?php endforeach; ?>
</ul>
<div class="rdc-svc__cta" data-rdc-pnl-en><?php echo \RadmanCT\button(__('More information','radmanct'),$rdct_web_url); ?></div>