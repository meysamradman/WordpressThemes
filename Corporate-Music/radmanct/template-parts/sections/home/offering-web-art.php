<?php
defined( 'ABSPATH' ) || exit;
$rdct_art = $args['media'];
$rdct_default_orbit = \RadmanCT\Home\OfferingArt\orbit_preset( 0 );
$rdct_tracks = $args['tracks'] ?? $rdct_default_orbit['tracks'];
$rdct_orbs = $args['orbs'] ?? $rdct_default_orbit['orbs'];
?>
<?php foreach ( array( 'back', 'front' ) as $rdct_layer ) : ?>
<svg class="rdc-orb__paths rdc-orb__paths--<?php echo esc_attr( $rdct_layer ); ?>" viewBox="0 0 1000 625" aria-hidden="true" focusable="false">
	<?php foreach ( $rdct_tracks as $rdct_track ) :
		$rdct_start = 500 + ( 'front' === $rdct_layer ? $rdct_track[0] : -$rdct_track[0] );
		$rdct_end = 1000 - $rdct_start;
		?>
	<path d="M<?php echo (int) $rdct_start; ?> 305 A<?php echo (int) $rdct_track[0]; ?> <?php echo (int) $rdct_track[1]; ?> 0 0 1 <?php echo (int) $rdct_end; ?> 305" transform="rotate(<?php echo (int) $rdct_track[2]; ?> 500 305)" <?php if ( 'back' === $rdct_layer ) { echo 'data-orbit-cx="500" data-orbit-cy="305" data-orbit-rx="' . (int) $rdct_track[0] . '" data-orbit-ry="' . (int) $rdct_track[1] . '" data-orbit-tilt="' . (int) $rdct_track[2] . '"'; if ( ! empty( $rdct_track[3] ) ) { echo ' data-orbit-duration="' . (int) $rdct_track[3] . '"'; } } ?> />
	<?php endforeach; ?>
</svg>
<?php endforeach; ?>
<?php echo wp_get_attachment_image( $rdct_art['base'], 'full', false, array( 'class' => 'rdc-orb__art', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 1023px) 90vw, 45vw' ) ); ?>
<?php foreach ( $rdct_orbs as $rdct_orb ) :
	$rdct_track = $rdct_tracks[ $rdct_orb[0] ]; $rdct_angle = $rdct_orb[1] * 2 * M_PI; $rdct_tilt = deg2rad( $rdct_track[2] );
	$rdct_x = $rdct_track[0] * cos( $rdct_angle ); $rdct_y = $rdct_track[1] * sin( $rdct_angle );
	$rdct_left = ( 500 + $rdct_x * cos( $rdct_tilt ) - $rdct_y * sin( $rdct_tilt ) ) / 10;
	$rdct_top = ( 305 + $rdct_x * sin( $rdct_tilt ) + $rdct_y * cos( $rdct_tilt ) ) / 6.25;
	?>
<?php echo wp_get_attachment_image( $rdct_art[ $rdct_orb[2] ], 'full', false, array(
	'class' => 'rdc-orb__orb', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '80px',
	'data-orbit-track' => (int) $rdct_orb[0], 'data-orbit-phase' => $rdct_orb[1],
	'style' => '--orb-size:' . $rdct_orb[3] . '%;left:' . round( $rdct_left, 3 ) . '%;top:' . round( $rdct_top, 3 ) . '%;z-index:' . ( sin( $rdct_angle ) > 0 ? 4 : 1 ),
) ); ?>
<?php endforeach; ?>
