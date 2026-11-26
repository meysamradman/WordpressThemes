<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$order_count = WC()->cart->get_cart_contents_count();
$is_empty    = WC()->cart->is_empty();
$sub_total   = WC()->cart->get_cart_subtotal();

?>
<span class="item-count" style="display:none;"><?php echo esc_attr( $order_count ); ?></span>
<div class="ld-cart-head d-flex align-items-center w-100 h2">
	<?php esc_html_e( 'Cart', 'hub' ); ?> <span class="ld-module-trigger-count d-flex align-items-centeer justify-content-center"><?php echo esc_html( $order_count ) ?></span>
</div>

<div class="ld-cart-products woocommerce-mini-cart-item w-100 pos-rel">

	<?php if ( liquid_helper()->get_option( "wc_cart_content_products_before" ) ) { echo wp_kses_post( liquid_helper()->get_option( "wc_cart_content_products_before" ) ); } ?>

	<?php if ( ! $is_empty ) : ?>

		<?php

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
					$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
					$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<div class="<?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'ld-cart-product d-flex flex-wrap align-items-center w-100 pos-rel', $cart_item, $cart_item_key ) ); ?>">
						<?php
						echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
							'<a href="%s" class="ld-cart-product-remove remove remove_from_cart_button" title="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"><svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" style="width: 1em; height: 1em;"><path fill="currentColor" d="M7.45 7.45a1.12 1.12 0 0 1 1.57 0l7.05 7.04 7.05-7.04a1.12 1.12 0 1 1 1.58 1.57l-7.04 7.05 7.04 7.05a1.12 1.12 0 1 1-1.58 1.58l-7.05-7.04-7.05 7.04a1.12 1.12 0 1 1-1.57-1.58l7.04-7.05-7.04-7.05a1.12 1.12 0 0 1 0-1.57z"></path></svg></a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							__( 'Remove this item', 'hub' ),
							esc_attr( $product_id ),
							esc_attr( $cart_item_key ),
							esc_attr( $_product->get_sku() )
						), $cart_item_key );
						?>
						<div class="ld-cart-product-info d-flex align-items-center flex-grow-1 pos-rel">
						<?php if ( empty( $product_permalink ) ) : ?>
							<?php if( $thumbnail ) { ?>
								<figure>
									<?php echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key ); ?>
								</figure>
							<?php } ?>
							<span class="ld-cart-product-details d-flex flex-column flex-grow-1">
								<span class="ld-cart-product-name h2 mt-0 mb-0"><?php echo wp_kses_post( $product_name ); ?></span>
								<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
							</span>
						<?php else : ?>
							<a class="d-flex align-items-center flex-grow-1" href="<?php echo esc_url( $product_permalink ); ?>">
								<?php if( $thumbnail ) { ?>
									<figure>
										<?php echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key ); ?>
									</figure>
								<?php } ?>
								<span class="ld-cart-product-details d-flex flex-column flex-grow-1">
									<span class="ld-cart-product-name h2 mt-0 mb-0"><?php echo wp_kses_post( $product_name ); ?></span>
									<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>

									<span class="ld-cart-product-price d-flex flex-column align-items-start">
										<?php echo esc_html( strip_tags( wc_price( $_product->get_price() * $cart_item['quantity'] ) ) ); ?>
									</span>

									<div class="ld-cart-product-price-qty" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>">
										<div class="ld-cart-product-price-qty-update" data-cart-action="decrease" data-product-id="<?php echo esc_attr( $product_id ); ?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /></svg>
										</div>
										<span><?php echo esc_html( $cart_item['quantity'] ); ?></span>
										<div class="ld-cart-product-price-qty-update" data-cart-action="increase" data-product-id="<?php echo esc_attr( $product_id ); ?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
										</div>
									</div>

								</span>
							</a>
						<?php endif; ?>
						</div>

					</div>
					<?php
				}
			}
		?>

	<?php else : ?>

		<?php
			printf( '<div class="empty"><%1$s>%2$s</%1$s></div>', liquid_helper()->get_option( 'wc_cart_tag', 'h3' ), esc_html__( 'No products in the cart.', 'hub' ) );
			$empty_msg = liquid_helper()->get_option( 'wc_cart_empty_text', 'post' );

			if ( ! empty( $empty_msg ) ) {
				printf( '<div class="ld-empty-cart-suggest__content mb-5">%s</div>', wpautop( $empty_msg ) );
			}
		?>

		<?php if ( 'on' === liquid_helper()->get_option( 'wc_mini_cart_enable_suggestions' ) ) : ?>
			<div class="ld-empty-cart-suggest w-100 text-center">
				<?php
				$popular_q = new WP_Query(
					array(
						'post_type'      => 'product',
						'meta_key'       => 'total_sales',
						'orderby'        => 'meta_value_num',
						'posts_per_page' => 3,
					)
				);

				if ( $popular_q->have_posts() ) {
					$slider_args = array(
						'prevNextButtons' => true,
						'buttonsAppendTo' => 'parent_el',
						'wrapAround'      => false,
						'navArrow'        => 4,
						'fullwidthSide'   => true,
					);
					?>
					<div class="ld-empty-cart-suggest__posts">
						<div class="text-left mb-3"><?php esc_html_e( 'Popular', 'hub' ); ?></div>
						<ul class="carousel-items reset-ul woocommerce" data-lqd-flickity='<?php echo esc_attr( json_encode( $slider_args ) ); ?>'>
							<?php
							while ( $popular_q->have_posts() ) {
								$popular_q->the_post();
								wc_get_template_part( 'content', 'product-list-minimal' );
							}

							wp_reset_postdata();
							?>
						</ul>
					</div>
					<?php
				}
				?>
			</div>
		<?php endif; ?>

	<?php endif; ?>

	<?php if ( liquid_helper()->get_option( "wc_cart_content_products_after" ) ) { echo wp_kses_post( liquid_helper()->get_option( "wc_cart_content_products_after" ) ); } ?>

</div>

<?php if ( !$is_empty ) : ?>
<div class="ld-cart-foot w-100">
	<?php Liquid_Shipping_Progress::instance()->render( 'off-canvas-cart' ); ?>
	<div class="ld-cart-total d-flex flex-wrap align-items-center justify-content-between">
		<span class="ld-cart-total-label font-weight-bold text-uppercase ltr-sp-2"><?php esc_html_e( 'Subtotal', 'hub' ); ?></span>
		<span class="ld-cart-total-price color-primary"><?php echo wp_kses_post( $sub_total ); ?></span>
	</div>
	<div class="ld-cart-button d-flex flex-column">
		<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-xlg btn-solid text-uppercase ltr-sp-2 text-center">
			<span>
				<span class="btn-txt"><?php esc_html_e( 'Checkout', 'hub' ); ?></span>
				<span class="btn-icon"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="32" viewBox="0 0 12 32" style="width: 1em; height: 1em;"><path fill="currentColor" d="M8.375 16L.437 8.062C-.125 7.5-.125 6.5.438 5.938s1.563-.563 2.126 0l9 9c.562.562.624 1.5.062 2.062l-9.063 9.063c-.312.312-.687.437-1.062.437s-.75-.125-1.063-.438c-.562-.562-.562-1.562 0-2.125z"></path></svg></span>
			</span>
		</a>
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn btn-naked text-uppercase ltr-sp-2 text-center">
			<span>
				<span class="btn-txt"><?php esc_html_e( 'View Cart', 'hub' ); ?></span>
				<span class="btn-icon"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="32" viewBox="0 0 12 32" style="width: 1em; height: 1em;"><path fill="currentColor" d="M8.375 16L.437 8.062C-.125 7.5-.125 6.5.438 5.938s1.563-.563 2.126 0l9 9c.562.562.624 1.5.062 2.062l-9.063 9.063c-.312.312-.687.437-1.062.437s-.75-.125-1.063-.438c-.562-.562-.562-1.562 0-2.125z"></path></svg></span>
			</span>
		</a>
	</div>
</div>
<?php endif; ?>