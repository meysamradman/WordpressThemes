<?php
/**
 * LiquidThemes WooCommerce hooks
 *
 * @package liquid-framework
 */

add_theme_support( 'wc-product-gallery-zoom' );
add_theme_support( 'wc-product-gallery-lightbox' );
add_theme_support( 'wc-product-gallery-slider' );


/**
 * Layout
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/**
 * Loop
 * @see  liquid_woocommere_headline()
 * @see  liquid_woocommerce_template_loop_product_title()
 */

add_action( 'woocommerce_before_shop_loop', 'get_shop_content', 5 );
add_action( 'woocommerce_before_shop_loop', 'liquid_start_shop_topbar_container', 10 );
add_action( 'woocommerce_before_shop_loop', 'liquid_end_shop_topbar_container', 90 );

add_action( 'woocommerce_before_shop_loop_item_title', 'liquid_get_secondary_product_image', 11 );
add_action( 'woocommerce_before_shop_loop_item_title', 'liquid_woocommerce_template_loop_product_gallery', 12 );

add_action( 'woocommerce_before_single_product', 'liquid_get_woo_header_notice', 12 );
add_action( 'woocommerce_before_single_product', 'liquid_start_shop_topbar_container', 15 );
add_action( 'woocommerce_before_single_product', 'liquid_end_shop_topbar_container', 90 );
add_action( 'woocommerce_before_single_product', 'woocommerce_breadcrumb', 20 );

add_filter( 'woocommerce_breadcrumb_defaults', 'liquid_woocommerce_breadcrumb_args', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating',               5 );
remove_action( 'woocommerce_shop_loop_item_title',       'woocommerce_template_loop_product_title',       10 );
add_action( 'woocommerce_shop_loop_item_title',          'liquid_woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 10 );

add_action( 'wp_enqueue_scripts', 'liquid_wc_enqueue_scripts', 10 );
add_action( 'woocommerce_layered_nav_count', 'liquid_wc_layered_nav_count', 10, 3 );
add_action( 'list_product_cats', 'liquid_wc_list_product_cats', 10, 2 );
add_action( 'body_class', 'liquid_wc_body_class', 99 );
add_action( 'wp_ajax_liquid_wc_filter_ajax', 'liquid_wc_filter_ajax' );
add_action( 'wp_ajax_nopriv_liquid_wc_filter_ajax', 'liquid_wc_filter_ajax' );
add_filter( 'woocommerce_loop_add_to_cart_args', 'liquid_wc_loop_add_to_cart_args', 10, 2 );
add_filter( 'woocommerce_price_filter_sql', 'liquid_wc_price_filter_sql' );
//add_filter( 'woocommerce_get_filtered_term_product_counts_query', 'liquid_wc_get_filtered_term_product_counts_query' );
//add_filter( 'woocommerce_product_query_tax_query', 'liquid_wc_product_query_tax_query' );

add_filter( 'liquid_ajax_add_to_cart_single_product', '__return_false', 5 );


/**
 * Loop List
 * @see liquid_woocommerce_add_to_cart_list()
 */
add_action( 'woocommerce_shop_loop_add_cart_list', 'liquid_woocommerce_add_to_cart_list', 10 );

/**
 * Loop carousel add to cart
 * @see liquid_woocommerce_add_to_cart_carousel()
 */
add_action( 'liquid_woocommerce_loop_add_to_cart_carousel', 'liquid_woocommerce_add_to_cart_carousel', 10 );

/**
 * Loop elegant add to cart
 * @see liquid_woocommerce_add_to_cart_elegant()
 */
add_action( 'liquid_woocommerce_loop_add_to_cart_elegant', 'liquid_woocommerce_add_to_cart_elegant', 10 );

/**
 * Product
 * @see  liquid_woocommerce_template_single_cats()
 * @see  liquid_woocommerce_variations_quantity_input()
 */
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash',         10 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

add_action( 'woocommerce_after_single_product', 'liquid_theme_init_js', 30 );

add_action( 'woocommerce_checkout_order_review', 'liquid_heading_payment_method', 15 );

/**
 * Filters
 * @see  liquid_woocommerce_body_class()
 * @see  liquid_products_per_page()
 * @see  liquid_loop_columns()
 * @see  liquid_wc_add_custom_query_var()
 * @see  liquid_woocommerce_catalog_orderby()
 */
add_filter( 'body_class',                           'liquid_woocommerce_body_class' );
add_filter( 'loop_shop_per_page',                   'liquid_wc_limit_archive_posts_per_page', 20 );
add_filter( 'loop_shop_columns',                    'liquid_loop_columns' );
add_filter( 'woocommerce_related_products_columns', 'liquid_related_loop_columns', 10, 1 );
add_filter( 'woocommerce_up_sells_columns',         'liquid_upsell_loop_columns', 10, 1 );
add_filter( 'woocommerce_cross_sells_columns',      'liquid_cross_sell_loop_columns', 10, 1 );
add_filter( 'query_vars',                           'liquid_wc_add_custom_query_var' );
add_filter( 'woocommerce_catalog_orderby',          'liquid_woocommerce_catalog_orderby' );

/**
 * Custom actions
 * @see  liquid_woocommerce_setup()
 */
add_action( 'after_switch_theme', 'liquid_woocommerce_setup', 1 );
add_action( 'init',               'liquid_woocommerce_clear_cart_url' );

/**
 * Custom metaboxes for products in general tab
 * @see  liquid_add_custom_general_fields()
 * @see  liquid_add_custom_general_fields_save()
 */
add_action( 'woocommerce_product_options_general_product_data', 'liquid_add_custom_general_fields' );
add_action( 'woocommerce_process_product_meta',                 'liquid_add_custom_general_fields_save' );

add_filter( 'woocommerce_add_to_cart_fragments', 'liquid_add_to_cart_fragments' );
add_filter( 'woocommerce_add_to_cart_fragments', 'liquid_add_to_cart_amount' );
add_filter( 'woocommerce_add_to_cart_fragments', 'liquid_add_to_cart_quickcart' );


function liquid_add_to_cart_fragments( $fragments ) {

	ob_start();
?>
	<span class="ld-module-trigger-count ld-module-trigger-count-sup header-cart-fragments"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
<?php

	$fragments['span.header-cart-fragments'] = ob_get_clean();
    return $fragments;

};

function liquid_add_to_cart_amount( $fragments ) {

	ob_start();
?>
	<span class="header-cart-amount"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
<?php

	$fragments['span.header-cart-amount'] = ob_get_clean();
    return $fragments;

};

function liquid_add_to_cart_quickcart( $fragments ) {

    ob_start();

?>
	<div class="header-quickcart"><?php liquid_woocommerce_header_cart() ?></div>
    <?php $fragments['div.header-quickcart'] = ob_get_clean();

    return $fragments;
};

function liquid_add_cart_single_ajax() {

	$product_id = $_POST['product_id'];
	$variation_id = $_POST['variation_id'];
	$quantity = $_POST['quantity'];
	$is_grouped = $_POST['isGrouped'] ?? false;
	$quantities = $_POST['quantities'] ?? array();

	if ($variation_id) {
		WC()->cart->add_to_cart( $product_id, $quantity, $variation_id );
	} else if ($is_grouped) {
		foreach ( $quantities as $product_id => $qty ) {
			$product_id = absint( $product_id );
			$qty        = absint( $qty );

			if ( $qty > 0 ) {
				WC()->cart->add_to_cart( $product_id, $qty );
			}
		}
	} else {
		WC()->cart->add_to_cart( $product_id, $quantity);
	}

	$items = WC()->cart->get_cart();
	global $woocommerce;
	$item_count = $woocommerce->cart->cart_contents_count;

	ob_start();
	liquid_woocommerce_header_cart();

	wp_send_json_success(
		array( 'html' => ob_get_clean() )
	);
}

add_action( 'wp_ajax_liquid_add_cart_single', 'liquid_add_cart_single_ajax' );
add_action( 'wp_ajax_nopriv_liquid_add_cart_single', 'liquid_add_cart_single_ajax' );

function liquid_update_qty_ajax() {
	$cart_item_key = sanitize_text_field( $_POST[ 'cartItemKey' ] );
	$type = sanitize_text_field( $_POST[ 'type' ] );

	if ( ! WC()->cart ) {
		wp_send_json_error( [
			'message' => esc_html__( 'Enable the WooCommerce.', 'hub' ),
		] );
	}

	$cart = WC()->cart->get_cart();

	if ( ! isset( $cart[ $cart_item_key ] ) ) {
		wp_send_json_error( [
			'message' => esc_html__( 'Product not found.', 'hub-ai-agent' ),
		] );
	}

	$current_qty = $cart[ $cart_item_key ]['quantity'];
	$new_qty = $current_qty;

	switch ( $type ) {
		case 'increase':
			$new_qty = $current_qty + 1;
			break;

		case 'decrease':
			$new_qty = max( 0, $current_qty - 1 );
			break;

		default:
			return false;
	}

	// If new quantity is 0 → remove item
	if ( $new_qty === 0 ) {
		WC()->cart->remove_cart_item( $cart_item_key );
	}

	// Update quantity
	WC()->cart->set_quantity( $cart_item_key, $new_qty );

	ob_start();
	liquid_woocommerce_header_cart();

	wp_send_json_success(
		array( 'html' => ob_get_clean() )
	);
}

add_action( 'wp_ajax_liquid_update_qty', 'liquid_update_qty_ajax' );
add_action( 'wp_ajax_nopriv_liquid_update_qty', 'liquid_update_qty_ajax' );

/**
 * Insert a value or key/value pair after a specific key in an array.  If key doesn't exist, value is appended
 * to the end of the array.
 *
 * @param array $array
 * @param string $key
 * @param array $new
 *
 * @return array
 */
function array_insert_after( array $array, $key, array $new ) {
	$keys = array_keys( $array );
	$index = array_search( $key, $keys );
	$pos = false === $index ? count( $array ) : $index + 1;

	return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
}

/**
 * Function for `woocommerce_settings_pages` filter-hook.
 *
 * @param  $settings
 *
 * @return
 */
function liquid_woocommerce_settings_pages_filter( $settings ){

	$lqd_settings = array();

	$lqd_settings[] = array(
		'title'    => __( 'Custom checkout page', 'hub' ),
		'desc'     => __( 'Select custom checkout page', 'hub' ),
		'id'       => 'liquid_woocommerce_checkout_page_id',
		'type'     => 'single_select_page_with_search',
		'default'  => '',
		'class'    => 'wc-page-search',
		'css'      => 'min-width:300px;',
		'args'     => array(
			'exclude' =>
				array(
					wc_get_page_id( 'cart' ),
					wc_get_page_id( 'checkout' ),
					wc_get_page_id( 'myaccount' ),
				),
		),
		'desc_tip' => true,
		'autoload' => false,
	);

	$lqd_settings[] = array(
		'title'    => __( 'Custom thank you page', 'hub' ),
		'desc'     => __( 'Select custom thank you page for all products.', 'hub' ),
		'id'       => 'liquid_woocommerce_thank_you_page_id',
		'type'     => 'single_select_page_with_search',
		'default'  => '',
		'class'    => 'wc-page-search',
		'css'      => 'min-width:300px;',
		'args'     => array(
			'exclude' =>
				array(
					wc_get_page_id( 'cart' ),
					wc_get_page_id( 'checkout' ),
					wc_get_page_id( 'myaccount' ),
				),
		),
		'desc_tip' => true,
		'autoload' => false,
	);
	$settings = array_insert_after($settings, '4', $lqd_settings);

	return $settings;

}
add_filter( 'woocommerce_settings_pages', 'liquid_woocommerce_settings_pages_filter' );

function liquid_get_ajax_products() {
	check_ajax_referer( 'liquid-nonce' );

	global $wpdb;

	$args     = $_POST['args'] ?? array();
	$paged    = isset( $_POST['paged'] ) ? absint( wp_unslash( $_POST['paged'] ) ) : 1;
	$order    = isset( $args['order'] ) ? sanitize_text_field( wp_unslash( $args['order'] ) ) : 'desc';
	$orderby  = isset( $args['orderby'] ) ? sanitize_text_field( wp_unslash( $args['orderby'] ) ) : 'date';
	$show     = isset( $args['show'] ) ? sanitize_text_field( wp_unslash( $args['show'] ) ) : '';
	$template = isset( $args['template'] ) ? sanitize_text_field( wp_unslash( $args['template'] ) ) : '';
	$category = isset( $args['category'] ) ? absint( wp_unslash( $args['category'] ) ) : 0;

	$args = array(
		'posts_per_page' => intval( $args['limit'] ) ? $args['limit'] : 12,
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'paged'          => $paged,
	);

	$args['tax_query'] = array(
		array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'exclude-from-catalog',
			'operator' => 'NOT IN',
		),
	);

	if ( ! empty( $category ) ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'id',
			'terms'    => $category
		);
	}

	$args['meta_query']   = array();
	$args['meta_query'][] = WC()->query->stock_status_meta_query();
	$args['meta_query']   = array_filter( $args['meta_query'] );

	// default - menu_order
	$args['orderby']  = 'menu_order title';
	$args['order']    = $order == 'desc' ? 'desc' : 'asc';
	$args['meta_key'] = '';

	switch ( $orderby ) {
		case 'date' :
			$args['orderby'] = 'date';
			$args['order']   = $order == 'asc' ? 'asc' : 'desc';
			break;
		case 'price' :
			$args['orderby']  = "meta_value_num {$wpdb->posts}.ID";
			$args['order']    = $order == 'desc' ? 'desc' : 'asc';
			$args['meta_key'] = '_price';
			break;
		case 'popularity' :
			$args['meta_key'] = 'total_sales';
			// Sorting handled later though a hook
			$args['orderby'] = "$wpdb->postmeta.meta_value+0 DESC, $wpdb->posts.post_date DESC";
			//add_filter('posts_clauses', 'liquid_woocommerce_order_by_popularity_post_clauses');
			break;
		case 'rating' :
			// Sorting handled later though a hook
			add_filter( 'posts_clauses', 'liquid_woocommerce_order_by_rating_post_clauses' );
			break;
		case 'title' :
			$args['orderby'] = 'title';
			$args['order']   = $order == 'desc' ? 'desc' : 'asc';
			break;
	}

	switch ( $show ) {
		case 'featured' :
			if ( $template == 'grid' ) {
				$args['meta_query'][] = array(
					'key'   => '_featured',
					'value' => 'yes',
				);
			} else {
				$args['post__in'] = wc_get_featured_product_ids();
			}
			break;
		case 'onsale' :
			$product_ids_on_sale   = wc_get_product_ids_on_sale();
			$product_ids_on_sale[] = 0;
			$args['post__in']      = $product_ids_on_sale;
			break;
	}

	$style = liquid_helper()->get_option( 'wc-archive-product-style' );

	$q = new \WP_Query( $args );

	if ( ! $q->have_posts() ) {
		wp_send_json_error();
	}

	ob_start();
	while ( $q->have_posts() ) {
		$q->the_post();

		if( 'minimal' === $style || 'minimal-2' === $style ) {
			wc_get_template_part( 'content', 'product-minimal' );
		}
		elseif( 'minimal-hover-shadow' === $style ) {
			wc_get_template_part( 'content', 'product-minimal-hover-shadow' );
		}
		elseif( 'minimal-hover-shadow-2' === $style ) {
			wc_get_template_part( 'content', 'product-minimal-hover-shadow-2' );
		}
		elseif( 'classic' === $style || 'classic-alt' === $style ) {
			wc_get_template_part( 'content', 'product-classic' );
		}
		elseif( 'digital' === $style ) {
			wc_get_template_part( 'content', 'product-digital' );
		}
		else {
			wc_get_template_part( 'content', 'product' );
		}
	}

	wp_reset_postdata();

	wp_send_json_success(
		array(
			'html' => ob_get_clean(),
			'last' => ( $q->max_num_pages <= $paged ),
		)
	);
}

add_action( 'wp_ajax_liquid_get_ajax_products', 'liquid_get_ajax_products' );
add_action( 'wp_ajax_nopriv_liquid_get_ajax_products', 'liquid_get_ajax_products' );

add_filter(
	'yith_wcqv_product_image_html',
	function () {
		ob_start();
		wc_get_template( 'single-product/product-image.php' );
		return ob_get_clean();
	}
);

add_action(
	'liquid_single_product_summary_content',
	function () {
		global $product;

		$product_id  = $product->get_id();
		$r_fit       = get_post_meta( $product_id, 'wc-r-fit', true );
		$r_fit_level = get_post_meta( $product_id, 'wc-r-fit-level', true );

		if ( empty( $r_fit ) || empty( $r_fit_level ) ) {
			return;
		}

		$theme_option = get_option( 'liquid_one_opt' )['wc-r-fits'] ?? [];
		$ids          = $theme_option['id'] ?? [];
		$i            = null;

		if ( ! empty( $ids ) ) {
			foreach ( $ids as $index => $id ) {
				if ( (int) $id === (int) $r_fit ) {
					$i = $index;
					break;
				}
			}
		}

		if ( is_null( $i ) ) {
			return;
		}

		$fit_title = $theme_option['title'][ $i ] ?? '';
		$fit_desc  = $theme_option['desc'][ $i ] ?? '';
		$fits      = $theme_option['fits'][ $i ] ?? [];
		$fits      = explode( '|', $fits );

		?>
		<div class="lqd-r-fits w-100">
			<?php if ( ! empty( $fit_title ) || ! empty( $fit_desc ) ) : ?>
				<div class="lqd-r-fits__head d-flex align-items-center">
					<span class="lqd-r-fits__title"><?php echo esc_html( $fit_title ); ?></span>
					<?php if ( ! empty( $fit_desc ) ) : ?>
						<div class="lqd-r-fits__desc d-flex align-items-center">
							<svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.125 0a8.125 8.125 0 1 0 8.125 8.125A8.133 8.133 0 0 0 8.125 0m0 15A6.875 6.875 0 1 1 15 8.125 6.883 6.883 0 0 1 8.125 15m1.25-3.125a.625.625 0 0 1-.625.625 1.25 1.25 0 0 1-1.25-1.25V8.125a.625.625 0 0 1 0-1.25 1.25 1.25 0 0 1 1.25 1.25v3.125a.625.625 0 0 1 .625.625m-2.5-7.187a.938.938 0 1 1 1.875 0 .938.938 0 0 1-1.875 0" fill="#000"/></svg>
							<div class="lqd-r-fits__tt"><?php echo esc_html( $fit_desc ); ?></div>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="lqd-r-fits__grid d-flex">
				<?php foreach ( $fits as $j => $fit ) : ?>
					<div class="lqd-r-fit d-flex flex-column justify-content-end<?php echo esc_attr( $j + 1 === (int) $r_fit_level ? ' lqd-r-fit--highlighted' : '' ); ?>">
						<span class="lqd-r-fit__title"><?php echo esc_html( $fit ); ?></span>
						<span class="lqd-r-fit__shape"></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
);

add_action(
	'template_redirect',
	function () {
		$upsell_position = liquid_helper()->get_option( 'ld_upsell_position' );

		if ( empty( $upsell_position ) ) {
			return;
		}

		if ( 'after_add_to_cart' !== $upsell_position ) {
			return;
		}

		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	}
);

add_action(
	'liquid_single_product_summary_after_cart',
	function () {
		global $product;

		$upsell_ids = $product->get_upsell_ids();

		if ( empty( $upsell_ids ) ) {
			return;
		}

		if ( 'after_add_to_cart' !== liquid_helper()->get_option( 'ld_upsell_position') ) {
			return;
		}

		$args = [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'post__in'       => $upsell_ids,
			'orderby'        => 'post__in',
		];

		$upsell_query = new WP_Query( $args );

		if ( ! $upsell_query->have_posts() ) {
			return;
		}

		$slider_args = array(
			'prevNextButtons' => true,
			'buttonsAppendTo' => 'parent_el',
			'wrapAround'      => true,
			'navArrow'        => 4,
			'fullwidthSide'   => true,
		);
		?>
		<div class="lqd-upsell-slider">
			<span class="lqd-upsell-slider__title"><?php esc_html_e( 'You may also like&hellip;', 'hub' ); ?></span>
			<ul class="carousel-items reset-ul woocommerce" data-lqd-flickity='<?php echo esc_attr( json_encode( $slider_args ) ); ?>'>
				<?php
				while ( $upsell_query->have_posts() ) {
					$upsell_query->the_post();
					wc_get_template_part( 'content', 'product-list-minimal' );
				}
				wp_reset_postdata();
				?>
			</ul>
		</div>
		<?php
	}
);

// Woocommerce Categories Block Filter
add_filter( 'render_block', function( $block_content, $block ) {
    // Target only the specific WooCommerce Product Categories block
    if ( isset( $block['blockName'] ) && $block['blockName'] === 'woocommerce/product-categories' ) {

        // Regex to find each list item and its link
        // Group 1: The opening <a> tag including the href
        // Group 2: The URL inside the href
        // Group 3: The rest of the content inside the <a>
        $pattern = '/(<a[^>]+href="([^"]+)"[^>]*>)(.*?<\/a>)/i';

        $block_content = preg_replace_callback($pattern, function($matches) {
            $full_opening_tag = $matches[1];
            $url = $matches[2];
            $inner_html = $matches[3];
            $term_id = 0;

            // Extract the slug from the URL (e.g., 'clothing/accessories/')
            $path = parse_url($url, PHP_URL_PATH);
            $segments = array_filter(explode('/', $path));
            $slug = end($segments); // Get the last part of the URL

            if ($slug) {
                $term = get_term_by('slug', $slug, 'product_cat');
                if ($term) {
                    $term_id = $term->term_id;
                }
            }

            // Construct the new HTML: Input + the original inner span/text
            $custom_html = '<input type="hidden" data-term-id="' . $term_id . '"></input>';

            return $full_opening_tag . $custom_html . $inner_html;
        }, $block_content);
    }

    return $block_content;
}, 10, 2 );
