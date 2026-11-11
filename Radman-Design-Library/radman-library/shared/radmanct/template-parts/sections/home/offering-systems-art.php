<?php
defined( 'ABSPATH' ) || exit;
$rdct_art = $args['media'];
$rdct_pipes = \RadmanCT\Home\OfferingArt\pipes();
$rdct_moving_pipes = array( 0, 4, 8 );
?>
<svg class="rdc-orb__paths rdc-pipes" viewBox="0 0 1440 900" aria-hidden="true" focusable="false">
	<?php foreach ( $rdct_pipes as $rdct_index => $rdct_pipe ) : ?>
	<path d="<?php echo esc_attr( $rdct_pipe ); ?>"<?php if ( in_array( $rdct_index, $rdct_moving_pipes, true ) ) : ?> data-orbit-pipe data-orbit-duration="<?php echo 7 + $rdct_index % 3; ?>"<?php endif; ?> />
	<?php endforeach; ?>
</svg>
<?php echo wp_get_attachment_image( $rdct_art['base'], 'full', false, array( 'class' => 'rdc-orb__art', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width:1023px) 90vw,45vw' ) ); ?>
<?php foreach ( $rdct_moving_pipes as $rdct_track_index => $rdct_index ) :
	echo wp_get_attachment_image( $rdct_art['gold'], 'full', false, array( 'class' => 'rdc-orb__orb', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '16px', 'data-orbit-track' => $rdct_track_index, 'data-orbit-phase' => $rdct_index / 9, 'style' => '--orb-size:1.65%;opacity:0;z-index:4;' ) );
endforeach; ?>
