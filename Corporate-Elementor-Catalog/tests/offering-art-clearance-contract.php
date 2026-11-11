<?php

define( 'ABSPATH', '/' );
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES ); }
function wp_get_attachment_image( ...$args ) { return ''; }
$args = array( 'media' => array( 'base' => 1, 'gold' => 2, 'ivory' => 3 ) );
$apps = ( $argv[1] ?? '' ) === 'apps';
require __DIR__ . '/../radmanct-child/inc/home/offering-art.php';
if ( $apps ) {
	$args = array_merge( $args, \RadmanCTChild\Home\OfferingArt\orbit_preset( 3 ) );
}
ob_start();
require __DIR__ . '/../radmanct-child/template-parts/client/home/offering-web-art.php';
ob_end_clean();
$image = imagecreatefromwebp( __DIR__ . '/../artifacts/design/client-media/home/offerings/' . ( $apps ? 'mobile-app-v1.webp' : 'website-art-base-v4.webp' ) );
if ( ! $image ) { throw new RuntimeException( 'Artwork is unavailable.' ); }
$width = imagesx( $image ); $height = imagesy( $image );
$radius = (int) ceil( 40 * $width / 1440 );
foreach ( $rdct_tracks as $track ) {
	foreach ( array( 0, .5 ) as $seam ) {
		foreach ( $apps ? array( 1 ) : array( 1, 1.16 ) as $scale ) {
			foreach ( array( -.008, 0, .008 ) as $delta ) {
				$phase = ( $seam + $delta ) * 2 * M_PI; $tilt = deg2rad( $track[2] );
				$x = 500 + $track[0] * cos( $phase ) * cos( $tilt ) - $track[1] * sin( $phase ) * sin( $tilt );
				$y = 305 + $track[0] * cos( $phase ) * sin( $tilt ) + $track[1] * sin( $phase ) * cos( $tilt );
				$px = (int) round( ( .5 + ( $x / 1000 - .5 ) / $scale ) * $width );
				$py = (int) round( ( .5 + ( $y / 625 - .5 ) / ( $scale * ( $height / $width ) / .625 ) ) * $height );
				for ( $dx = -$radius; $dx <= $radius; $dx += 5 ) {
					for ( $dy = -$radius; $dy <= $radius; $dy += 5 ) {
						if ( $dx * $dx + $dy * $dy > $radius * $radius ) { continue; }
						$sample_x = $px + $dx; $sample_y = $py + $dy;
						if ( $sample_x < 0 || $sample_y < 0 || $sample_x >= $width || $sample_y >= $height ) { continue; }
						$color = imagecolorsforindex( $image, imagecolorat( $image, $sample_x, $sample_y ) );
						if ( $color['alpha'] < 124 ) { throw new RuntimeException( 'Orbit changes depth over visible artwork: ' . implode( ',', $track ) ); }
					}
				}
			}
		}
	}
}
imagedestroy( $image );
echo "Passed: full orb clearance around all four depth seams at mobile and desktop artwork scales.\n";
