<?php

namespace RadmanCTComponents\Components\Testimonial;

defined( 'ABSPATH' ) || exit;

function presentation(): array {
	$specs = array(
		'quote_size' => array( __( 'Review font size (px)', 'radmanct-components' ), '.rdct-tst__quote', 'font-size:%spx;', 120, 18 ),
		'quote_line_height' => array( __( 'Review line height', 'radmanct-components' ), '.rdct-tst__quote', 'line-height:%s;', 5, 1.85 ),
		'name_size' => array( __( 'Reviewer name size (px)', 'radmanct-components' ), '.rdct-tst__author cite', 'font-size:%spx;', 80, 18 ),
		'position_size' => array( __( 'Reviewer position size (px)', 'radmanct-components' ), '.rdct-tst__author > span', 'font-size:%spx;', 64, 14 ),
		'avatar_size' => array( __( 'Avatar size (px)', 'radmanct-components' ), '.rdct-tst__avatar', 'inline-size:%spx;block-size:%spx;', 240, 64 ),
		'logo_width' => array( __( 'Client logo width (px)', 'radmanct-components' ), '.rdct-tst__logo', 'max-inline-size:%spx;', 400, 100 ),
		'logo_height' => array( __( 'Client logo height (px)', 'radmanct-components' ), '.rdct-tst__logo', 'max-block-size:%spx;', 240, 48 ),
		'rating_size' => array( __( 'Rating size (px)', 'radmanct-components' ), '.rdct-tst__rating', 'font-size:%spx;', 64, 16 ),
		'quote_mark_size' => array( __( 'Quote mark size (px)', 'radmanct-components' ), '.rdct-tst__quote-mark', 'font-size:%spx;', 160, 64 ),
	);
	$result = array();
	foreach ( $specs as $key => $spec ) { $result[ 'testimonial_' . $key ] = array( 'label' => $spec[0], 'selector' => '& ' . $spec[1], 'css' => $spec[2], 'direct' => true, 'min' => 0, 'max' => $spec[3], 'default' => $spec[4], 'step' => .1 ); }
	$result['testimonial_avatar_size']['selector'] = '& .rdct-tst__avatar, &.rdct-tst--s04 .rdct-tst__portrait';
	foreach ( array( 'quote' => array( '.rdct-tst__quote', __( 'Review text', 'radmanct-components' ) ), 'name' => array( '.rdct-tst__author cite', __( 'Name', 'radmanct-components' ) ), 'position' => array( '.rdct-tst__author > span', __( 'Position or company', 'radmanct-components' ) ) ) as $key => $part ) {
		$result[ 'testimonial_' . $key . '_weight' ] = array( 'label' => sprintf( __( '%s font weight', 'radmanct-components' ), $part[1] ), 'direct' => true, 'default' => '', 'selector' => '& ' . $part[0], 'css' => 'font-weight:%s;', 'options_ui' => 'select', 'options' => array( '' => __( 'Inherit', 'radmanct-components' ), '400' => '400', '500' => '500', '600' => '600', '700' => '700', '800' => '800', '900' => '900' ) );
	}
	return $result;
}
