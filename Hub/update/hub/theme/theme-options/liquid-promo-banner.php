<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$this->sections[] = array(
	'title'  => esc_html__( 'Promo Banner', 'hub' ),
	'icon'   => 'el-icon-certificate',
	'fields' => array(
		array(
			'id'       => 'promo-template',
			'type'     => 'select',
			'title'    => esc_html__( 'Template', 'hub' ),
			'subtitle' => esc_html__( 'Select a promo banner template for your website', 'hub' ),
			'data'     => 'post',
			'args'     => array( 'post_type' => 'elementor_library', 'posts_per_page' => - 1 ),
		),
		array(
			'id'       => 'promo-template-pages',
			'type'     => 'select',
			'title'    => esc_html__( 'Show Only in These Pages', 'hub' ),
			'subtitle' => esc_html__( 'Leave empty to display all pages.', 'hub' ),
			'data'     => 'post',
			'multi'    => true,
			'args'     => array( 'post_type' => 'page', 'posts_per_page' => - 1 ),
		),
	),
);
