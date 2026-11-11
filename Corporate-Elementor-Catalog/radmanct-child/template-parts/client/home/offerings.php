<?php
defined( 'ABSPATH' ) || exit;
$rdct_offerings = array(
	array( 'web', __( 'Custom website design', 'radmanct-child' ), __( 'Tailored', 'radmanct-child' ), __( 'We design and develop modern, fast websites that fit your brand. From the first idea to launch, we are with you.', 'radmanct-child' ), array( __( 'Security and stability', 'radmanct-child' ), __( 'Responsive design', 'radmanct-child' ), __( 'Speed and performance', 'radmanct-child' ) ) ),
	array( 'wordpress', __( 'WordPress design and development', 'radmanct-child' ), __( 'WordPress websites', 'radmanct-child' ), __( 'Professional WordPress solutions: custom themes and plugins, easy content management, performance and security built for growth.', 'radmanct-child' ), array( __( 'Performance and scalability', 'radmanct-child' ), __( 'Custom development', 'radmanct-child' ), __( 'Multisite network', 'radmanct-child' ) ) ),
	array( 'systems', __( 'Management panel design', 'radmanct-child' ), __( 'and web applications', 'radmanct-child' ), __( 'Custom management panels and powerful web applications, built around your business with a scalable, modular architecture.', 'radmanct-child' ), array( __( 'Security and access control', 'radmanct-child' ), __( 'APIs and integrations', 'radmanct-child' ), __( 'Scalable architecture', 'radmanct-child' ) ) ),
	array( 'apps', __( 'Application design and development', 'radmanct-child' ), __( 'Mobile applications', 'radmanct-child' ), __( 'Custom Android and iOS applications with a smooth user experience, reliable performance and seamless connections to your business systems.', 'radmanct-child' ), array( __( 'Android and iOS', 'radmanct-child' ), __( 'User experience', 'radmanct-child' ), __( 'APIs and integrations', 'radmanct-child' ) ) ),
);
?>
<section class="rdct-clt-next rdct-clt-offers rdct-scheme-fixed" aria-label="<?php esc_attr_e( 'What we build', 'radmanct-child' ); ?>" data-rdct-header-tone="light">
	<?php
	if ( function_exists( 'rdct_sticky_sections' ) ) {
		$rdct_offer_items = array_map( static fn( $item ) => array( 'title' => '', 'content' => $item[1], 'layout' => 'content', 'background' => '#ece9e2', 'color' => '#20211f' ), $rdct_offerings );
		rdct_sticky_sections( $rdct_offer_items, array( 'mode' => 'services', 'transition' => 'slide-scale', 'custom_item' => true, 'min_scale' => .9, 'offset' => 20, 'gap' => 0, 'height' => 120, 'padding' => 0, 'radius' => 28, 'min_width' => 1024 ), null, static function ( $index ) use ( $rdct_offerings ): void {
			get_template_part( 'template-parts/client/home/offering-panel', null, array( 'index' => $index, 'item' => $rdct_offerings[ $index ], 'component_inner' => true ) );
		} );
	} else {
		foreach ( $rdct_offerings as $rdct_offer_index => $rdct_offer ) { get_template_part( 'template-parts/client/home/offering-panel', null, array( 'index' => $rdct_offer_index, 'item' => $rdct_offer ) ); }
	}
	?>
</section>
