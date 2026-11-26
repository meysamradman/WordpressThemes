<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Liquid_Multicurrency_Cart extends Liquid_Singleton {
	public function init() {
		add_filter( 'woocommerce_shipping_free_shipping_is_available', [ $this, 'adjust_free_shipping_threshold' ], 20, 3 );
		add_filter( 'woocommerce_shipping_method_add_rate_args', [ $this, 'adjust_shipping_cost' ] );
		add_filter( 'woocommerce_coupon_is_valid', [ $this, 'validate_coupon_amount' ], 10, 2 );
		add_filter( 'woocommerce_cart_needs_shipping', [ $this, 'check_minimum_order_amount' ], 10, 1 );
	}

	public function adjust_free_shipping_threshold( $is_available, $package, $shipping_method ) {
		$selected_currency = \Liquid_Multicurrency_Rates::get_selected_currency();
		$base_currency     = \Liquid_Multicurrency_Rates::get_base_currency();

		if ( $selected_currency === $base_currency ) {
			return $is_available;
		}

		$min_amount = $shipping_method->min_amount;

		if ( empty( $min_amount ) ) {
			return $is_available;
		}

		$converted_min_amount = \Liquid_Multicurrency_Rates::convert( $min_amount, $selected_currency );

		$cart_total = WC()->cart->get_displayed_subtotal();

		if ( 'incl' === WC()->cart->get_tax_price_display_mode() ) {
			$cart_total += WC()->cart->get_taxes_total();
		}

		if ( $cart_total >= $converted_min_amount ) {
			return true;
		}

		return false;
	}

	public function adjust_shipping_cost( $args ) {
		$selected_currency = \Liquid_Multicurrency_Rates::get_selected_currency();
		$base_currency     = \Liquid_Multicurrency_Rates::get_base_currency();

		if ( $selected_currency === $base_currency ) {
			return $args;
		}

		if ( isset( $args['cost'] ) && is_numeric( $args['cost'] ) ) {
			$args['cost'] = \Liquid_Multicurrency_Rates::convert( $args['cost'], $selected_currency );
		}

		return $args;
	}

	public function validate_coupon_amount( $valid, $coupon ) {
		$selected_currency = \Liquid_Multicurrency_Rates::get_selected_currency();
		$base_currency     = \Liquid_Multicurrency_Rates::get_base_currency();

		if ( $selected_currency === $base_currency ) {
			return $valid;
		}

		$minimum_amount = $coupon->get_minimum_amount();
		$maximum_amount = $coupon->get_maximum_amount();

		if ( $minimum_amount > 0 ) {
			$converted_min = \Liquid_Multicurrency_Rates::convert( $minimum_amount, $selected_currency );
			$cart_total    = WC()->cart->get_displayed_subtotal();

			if ( $cart_total < $converted_min ) {
				return false;
			}
		}

		if ( $maximum_amount > 0 ) {
			$converted_max = \Liquid_Multicurrency_Rates::convert( $maximum_amount, $selected_currency );
			$cart_total    = WC()->cart->get_displayed_subtotal();

			if ( $cart_total > $converted_max ) {
				return false;
			}
		}

		return $valid;
	}

	public function check_minimum_order_amount( $needs_shipping ) {
		$selected_currency = \Liquid_Multicurrency_Rates::get_selected_currency();
		$base_currency     = \Liquid_Multicurrency_Rates::get_base_currency();

		$minimum_order_amount = get_option( 'woocommerce_minimum_order_amount', 0 );

		if ( $minimum_order_amount > 0 && $selected_currency !== $base_currency ) {
			$converted_minimum = \Liquid_Multicurrency_Rates::convert( $minimum_order_amount, $selected_currency );
			$cart_total        = WC()->cart->get_displayed_subtotal();

			if ( $cart_total < $converted_minimum ) {
				wc_add_notice(
					sprintf(
						__( 'Minimum order amount %s', 'hub' ),
						wc_price( $converted_minimum, array( 'currency' => $selected_currency ) )
					),
					'error'
				);
			}
		}

		return $needs_shipping;
	}

	public static function get_cart_total_in_base_currency() {
		$selected_currency = \Liquid_Multicurrency_Rates::get_selected_currency();
		$base_currency     = \Liquid_Multicurrency_Rates::get_base_currency();

		$cart_total = WC()->cart->get_cart_contents_total();

		if ( $selected_currency !== $base_currency ) {
			return \Liquid_Multicurrency_Rates::convert( $cart_total, $base_currency );
		}

		return $cart_total;
	}

	public static function calculate_free_shipping_remaining() {
		$shipping_methods = WC()->shipping()->get_shipping_methods();

		if ( ! isset( $shipping_methods['free_shipping'] ) ) {
			return null;
		}

		$free_shipping = $shipping_methods['free_shipping'];
		$min_amount    = $free_shipping->min_amount;

		if ( empty( $min_amount ) ) {
			return 0;
		}

		$selected_currency = \Liquid_Multicurrency_Rates::get_selected_currency();
		$base_currency     = \Liquid_Multicurrency_Rates::get_base_currency();

		if ( $selected_currency !== $base_currency ) {
			$min_amount = \Liquid_Multicurrency_Rates::convert( $min_amount, $selected_currency );
		}

		$cart_total = WC()->cart->get_displayed_subtotal();
		$remaining  = $min_amount - $cart_total;

		return max( 0, $remaining );
	}
}
