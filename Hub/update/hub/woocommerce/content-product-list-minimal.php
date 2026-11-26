<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;

}
?>
<li <?php wc_product_class( '', $product ); ?>>
	<div class="ld-sp ld-sp-list-minimal">
		<div class="ld-sp-list-minimal__grid d-flex">
			<div class="ld-sp-list-minimal__left">
				<figure class="ld-sp-img">
					<?php
					woocommerce_template_loop_product_link_open();
					woocommerce_template_loop_product_thumbnail();
					woocommerce_template_loop_product_link_close();
					?>
				</figure>
			</div>
			<div class="ld-sp-list-minimal__right">
				<div class="ld-sp-info d-flex flex-column">
					<?php
					do_action( 'woocommerce_shop_loop_item_title' );
					woocommerce_template_loop_price();
					woocommerce_template_loop_add_to_cart();
					?>
				</div>
			</div>
		</div>
	</div>
</li>