<?php
defined( 'ABSPATH' ) || exit;
$rdct_art = $args['media'];
$rdct_routes = array(
	array( 375, 290, 330, 240, 300, 240, 285, 183 ),
	array( 625, 300, 650, 220, 700, 240, 716, 188 ),
	array( 360, 350, 310, 365, 280, 400, 220, 420 ),
	array( 650, 355, 700, 370, 730, 410, 795, 430 ),
	array( 485, 425, 435, 475, 400, 475, 480, 535 ),
);
?>
<svg class="rdct-clt-orbit__paths rdct-clt-network__paths" viewBox="0 0 1000 625" aria-hidden="true" focusable="false">
	<?php foreach ( $rdct_routes as $rdct_route_index => $rdct_route ) : ?>
	<path d="<?php echo esc_attr( vsprintf( 'M%s %s C%s %s %s %s %s %s', $rdct_route ) ); ?>" data-orbit-route="<?php echo esc_attr( implode( ',', $rdct_route ) ); ?>" data-orbit-duration="<?php echo 9 + $rdct_route_index * 2; ?>" />
	<?php endforeach; ?>
</svg>
<?php echo wp_get_attachment_image( $rdct_art['base'], 'full', false, array( 'class' => 'rdct-clt-orbit__art', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width:1023px) 90vw,45vw' ) ); ?>
<?php $rdct_sheen_id = wp_unique_id( 'rdct-coin-' ); ?>
<svg class="rdct-clt-coin-sheen" viewBox="0 0 1000 625" aria-hidden="true" focusable="false">
	<defs>
		<clipPath id="<?php echo esc_attr( $rdct_sheen_id ); ?>"><ellipse cx="498" cy="169" rx="78" ry="91" transform="rotate(20 498 169)" /></clipPath>
		<linearGradient id="<?php echo esc_attr( $rdct_sheen_id . '-light' ); ?>"><stop stop-color="#fff8df" stop-opacity="0"/><stop offset=".5" stop-color="#fff8df" stop-opacity=".32"/><stop offset="1" stop-color="#fff8df" stop-opacity="0"/></linearGradient>
	</defs>
	<g clip-path="url(#<?php echo esc_attr( $rdct_sheen_id ); ?>)"><rect data-art-sheen x="350" y="45" width="48" height="260" fill="url(#<?php echo esc_attr( $rdct_sheen_id . '-light' ); ?>)" /></g>
</svg>
<?php foreach ( $rdct_routes as $rdct_route_index => $rdct_route ) :
	$rdct_t = .18 + $rdct_route_index * .12; $rdct_u = 1 - $rdct_t;
	$rdct_x = $rdct_u ** 3 * $rdct_route[0] + 3 * $rdct_u ** 2 * $rdct_t * $rdct_route[2] + 3 * $rdct_u * $rdct_t ** 2 * $rdct_route[4] + $rdct_t ** 3 * $rdct_route[6];
	$rdct_y = $rdct_u ** 3 * $rdct_route[1] + 3 * $rdct_u ** 2 * $rdct_t * $rdct_route[3] + 3 * $rdct_u * $rdct_t ** 2 * $rdct_route[5] + $rdct_t ** 3 * $rdct_route[7];
	echo wp_get_attachment_image( $rdct_art['gold'], 'full', false, array( 'class' => 'rdct-clt-orbit__orb', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '32px', 'data-orbit-track' => $rdct_route_index, 'data-orbit-phase' => $rdct_t, 'style' => '--orb-size:3.6%;left:' . round( $rdct_x / 10, 3 ) . '%;top:' . round( $rdct_y / 6.25, 3 ) . '%;z-index:1;' ) );
endforeach; ?>
