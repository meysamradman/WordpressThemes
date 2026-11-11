<?php

namespace RadmanCT\Home\OfferingArt;

defined( 'ABSPATH' ) || exit;
const MEDIA = array(
	array( 'base' => 'offering-web', 'gold' => 'orb-gold', 'ivory' => 'orb-ivory' ),
	array( 'base' => 'offering-wordpress', 'gold' => 'orb-gold' ),
	array( 'base' => 'offering-systems', 'gold' => 'orb-gold' ),
	array( 'base' => 'offering-apps', 'gold' => 'orb-gold', 'ivory' => 'orb-ivory' ),
);

const VIEWPORTS = array(
	1 => array( 50, 20, 1366, 874 ),
	2 => array( 168, 62, 1150, 792 ),
	3 => array( 90, 0, 1260, 900 ),
);

function viewport_style( int $index ): string {
	if ( ! isset( VIEWPORTS[ $index ] ) ) { return ''; }
	list( $x, $y, $width, $height ) = VIEWPORTS[ $index ];
	return '--rdc-art-rat:' . $width . '/' . $height . ';--rdc-art-left:' . round( -100 * $x / $width, 5 ) . '%;--rdc-art-top:' . round( -100 * $y / $height, 5 ) . '%;--rdc-art-w:' . round( 144000 / $width, 5 ) . '%;--rdc-art-ht:' . round( 90000 / $height, 5 ) . '%;';
}

function orbit_preset( int $index ): array {
	if ( 0 === $index ) { return array(
		'tracks' => array( array( 455, 195, -17, 51 ), array( 550, 215, 28, 63 ) ),
		'orbs' => array( array( 0, .08, 'gold', 5.7 ), array( 0, .76, 'ivory', 7.6 ), array( 1, .21, 'gold', 4.8 ), array( 1, .56, 'ivory', 6.7 ) ),
	); }
	return 3 === $index ? array(
		'tracks' => array( array( 418, 216, -15, 51 ), array( 450, 192, 24, 63 ) ),
		'orbs' => array( array( 0, .08, 'gold', 4.3 ), array( 0, .52, 'ivory', 4.8 ), array( 1, .22, 'gold', 3.6 ), array( 1, .66, 'ivory', 3.8 ) ),
	) : array();
}

function available( int $post_id ): bool {
	foreach ( array_keys( MEDIA ) as $index ) {
		if ( media( $post_id, $index ) ) { return true; }
	}
	return false;
}

function pipes(): array {
	return array(
		'M425 135 L492 138 Q521 138 521 164 V239 Q521 265 549 265 L587 266 Q612 266 612 290 V304',
		'M425 298 H461 Q487 298 487 324 V367 Q487 387 510 387 H526',
		'M425 455 H462 Q480 455 486 441 Q493 428 508 428 H526',
		'M655 304 V202 Q655 178 680 178 H714 Q725 178 730 185 Q738 190 757 190',
		'M817 426 H837 Q862 426 862 450 V593',
		'M675 546 V641 Q675 668 699 668 H729',
		'M938 665 H957 Q982 665 982 640 V607 Q982 584 1005 584 H1040',
		'M985 716 V713 Q985 693 1005 693 H1040',
		'M938 665 H957 Q982 665 982 689 V776 Q982 802 1005 802 H1040',
	);
}

function media( int $post_id, int $index = 0 ): array {
	$data = array_map( '\\RadmanCT\\media_id', MEDIA[ $index ] ?? array() );
	foreach ( $data as $id ) { if ( ! wp_attachment_is_image( $id ) ) { return array(); } }
	return $data;
}
