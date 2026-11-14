<?php
defined( 'ABSPATH' ) || exit;
$rdct_offer = $args['item'];
$rdct_offer_index = (int) $args['index'];
$rdct_offer_image = \RadmanCT\Home\OfferingArt\media( get_the_ID(), $rdct_offer_index )['base'] ?? 0;
$rdct_web_art = \RadmanCT\Home\OfferingArt\media( get_the_ID(), $rdct_offer_index );
$rdct_art_vendor = $rdct_web_art ? \RadmanCT\Assets\entry( 'src/js/gsap-vendor.js' ) : array();
$rdct_art_args = array_merge( array( 'media' => $rdct_web_art ), \RadmanCT\Home\OfferingArt\orbit_preset( $rdct_offer_index ) );
$rdct_art_viewport = $rdct_web_art ? \RadmanCT\Home\OfferingArt\viewport_style( $rdct_offer_index ) : '';
?>
<div class="<?php echo ! empty( $args['component_inner'] ) ? 'rdc-ss__inr ' : ''; ?>rdc-svc rdc-svc--<?php echo esc_attr( $rdct_offer[0] ); ?>">
	<div class="rdc-svc__copy">
		<?php get_template_part( 'template-parts/sections/home/offering-web-copy', null, array( 'item' => $rdct_offer, 'index' => $rdct_offer_index ) ); ?>
	</div>
	<figure class="rdc-svc__vis<?php echo $rdct_web_art ? ' rdc-orb' . ( 3 === $rdct_offer_index ? ' rdc-app-art' : ( $rdct_offer_index ? ' rdc-net' : '' ) ) : ''; ?>" data-rdc-pnl-vis aria-hidden="true" <?php if ( $rdct_art_viewport ) { echo 'style="' . esc_attr( $rdct_art_viewport ) . '"'; } elseif ( $rdct_web_art && $rdct_art_vendor ) { echo 'data-rdc-ofr-art data-gsap-src="' . esc_url( \RadmanCT\Assets\url( $rdct_art_vendor['file'] ) ) . '"'; } ?>>
		<?php if ( $rdct_art_viewport ) : ?><div class="rdc-art-stg" <?php if ( $rdct_art_vendor ) { echo 'data-rdc-ofr-art data-gsap-src="' . esc_url( \RadmanCT\Assets\url( $rdct_art_vendor['file'] ) ) . '"'; } ?>><?php endif; ?>
		<?php if ( $rdct_web_art ) { get_template_part( 'template-parts/sections/home/' . ( 2 === $rdct_offer_index ? 'offering-systems-art' : ( 1 === $rdct_offer_index ? 'offering-wordpress-art' : 'offering-web-art' ) ), null, $rdct_art_args ); }
		elseif ( $rdct_offer_image && wp_attachment_is_image( $rdct_offer_image ) ) { echo wp_get_attachment_image( $rdct_offer_image, 'large', false, array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 1023px) 90vw, 45vw' ) ); } ?>
		<?php if ( $rdct_art_viewport ) : ?></div><?php endif; ?>
	</figure>
</div>
