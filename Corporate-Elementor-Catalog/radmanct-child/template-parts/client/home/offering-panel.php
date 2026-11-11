<?php
defined( 'ABSPATH' ) || exit;
$rdct_offer = $args['item'];
$rdct_offer_index = (int) $args['index'];
$rdct_offer_image = \RadmanCTChild\Home\OfferingArt\MEDIA[ $rdct_offer_index ]['base'] ?? 0;
$rdct_web_art = \RadmanCTChild\Home\OfferingArt\media( get_the_ID(), $rdct_offer_index );
$rdct_art_vendor = $rdct_web_art ? \RadmanCT\Assets\entry( '../radmanct-components/src/js/gsap-vendor.js' ) : array();
$rdct_art_args = array_merge( array( 'media' => $rdct_web_art ), \RadmanCTChild\Home\OfferingArt\orbit_preset( $rdct_offer_index ) );
$rdct_art_viewport = $rdct_web_art ? \RadmanCTChild\Home\OfferingArt\viewport_style( $rdct_offer_index ) : '';
?>
<div class="<?php echo ! empty( $args['component_inner'] ) ? 'rdct-ss__inner ' : ''; ?>rdct-clt-offer rdct-clt-offer--<?php echo esc_attr( $rdct_offer[0] ); ?>">
	<div class="rdct-clt-offer__copy">
		<?php get_template_part( 'template-parts/client/home/offering-web-copy', null, array( 'item' => $rdct_offer, 'index' => $rdct_offer_index ) ); ?>
	</div>
	<figure class="rdct-clt-offer__visual<?php echo $rdct_web_art ? ' rdct-clt-orbit' . ( 3 === $rdct_offer_index ? ' rdct-clt-app-art' : ( $rdct_offer_index ? ' rdct-clt-network' : '' ) ) : ''; ?>" data-rdct-panel-visual aria-hidden="true" <?php if ( $rdct_art_viewport ) { echo 'style="' . esc_attr( $rdct_art_viewport ) . '"'; } elseif ( $rdct_web_art && $rdct_art_vendor ) { echo 'data-rdct-offer-art data-gsap-src="' . esc_url( \RadmanCT\Assets\url( $rdct_art_vendor['file'] ) ) . '"'; } ?>>
		<?php if ( $rdct_art_viewport ) : ?><div class="rdct-clt-art-stage" <?php if ( $rdct_art_vendor ) { echo 'data-rdct-offer-art data-gsap-src="' . esc_url( \RadmanCT\Assets\url( $rdct_art_vendor['file'] ) ) . '"'; } ?>><?php endif; ?>
		<?php if ( $rdct_web_art ) { get_template_part( 'template-parts/client/home/' . ( 2 === $rdct_offer_index ? 'offering-systems-art' : ( 1 === $rdct_offer_index ? 'offering-wordpress-art' : 'offering-web-art' ) ), null, $rdct_art_args ); }
		elseif ( $rdct_offer_image && wp_attachment_is_image( $rdct_offer_image ) ) { echo wp_get_attachment_image( $rdct_offer_image, 'large', false, array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 1023px) 90vw, 45vw' ) ); } ?>
		<?php if ( $rdct_art_viewport ) : ?></div><?php endif; ?>
	</figure>
</div>
