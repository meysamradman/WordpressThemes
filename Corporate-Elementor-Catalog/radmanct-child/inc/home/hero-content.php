<?php

namespace RadmanCTChild\Home;

defined( 'ABSPATH' ) || exit;
const HERO_IMAGE_ID = 12;
const HERO_CLEAN_IMAGE_ID = 13423;

function hero_content(): array {
	return array(
		'heading' => __( 'Nilgoon Tejarat Deniz', 'radmanct-child' ),
		'subheading' => __( 'Supplier of chemical raw materials', 'radmanct-child' ),
		'accent' => __( 'Industrial equipment', 'radmanct-child' ) . "\n" . __( 'Chemical raw materials', 'radmanct-child' ),
		'description' => trim( str_repeat( __( 'Nilgoon Tejarat Deniz', 'radmanct-child' ) . ' ', 8 ) ),
		'button_label' => __( 'Consultation', 'radmanct-child' ),
		'button_url' => '',
		'image_id' => HERO_IMAGE_ID,
	);
}
