<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Liquid_Multicurrency_Rates extends Liquid_Singleton {
	public function init() {
		add_filter( 'woocommerce_product_get_price', [ $this, 'apply' ] );
		add_filter( 'woocommerce_product_get_regular_price', [ $this, 'apply' ] );
		add_filter( 'woocommerce_product_get_sale_price', [ $this, 'apply' ] );
		add_filter( 'woocommerce_product_variation_get_price', [ $this, 'apply' ] );
		add_filter( 'woocommerce_product_variation_get_regular_price', [ $this, 'apply' ] );
		add_filter( 'woocommerce_product_variation_get_sale_price', [ $this, 'apply' ] );
		add_filter( 'woocommerce_currency_symbol', [ $this, 'apply_currency' ], 10, 2 );
	}

	public function apply( $price ) {
		if ( empty( $price ) ) {
			return $price;
		}

		return self::convert_price( $price );
	}

	public function apply_currency( $currency_symbol, $currency ) {
		$selected_currency = self::get_selected_currency();

		if ( $currency !== $selected_currency ) {
			return get_woocommerce_currency_symbol( $selected_currency );
		}

		return $currency_symbol;
	}

	public static function get_base_currency() {
		return get_woocommerce_currency();
	}

	public static function convert( $price, $to_currency ) {
		$to_rate = Liquid_Multicurrency_DB::get_rate( $to_currency );

		return $price * $to_rate;
	}

	public static function convert_price( $price ) {
		$selected_currency = self::get_selected_currency();
		$base_currency     = self::get_base_currency();

		if ( $selected_currency === $base_currency ) {
			return $price;
		}

		return self::convert( $price, $selected_currency );
	}

	public static function get_selected_currency() {
		$enabled_currencies = Liquid_Multicurrency_DB::get_enabled_currencies();

		if ( isset( $_COOKIE['liquid_m_currency'] ) ) {
			$currency = sanitize_text_field( $_COOKIE['liquid_m_currency'] );

			if ( in_array( $currency, $enabled_currencies ) ) {
				return $currency;
			}
		}

		return self::get_base_currency();
	}

	public static function set_currency( $currency_code ) {
		$enabled_currencies = Liquid_Multicurrency_DB::get_enabled_currencies();

		if ( $currency_code !== self::get_base_currency() && ! in_array( $currency_code, $enabled_currencies ) ) {
			return false;
		}

		setcookie( 'liquid_m_currency', $currency_code, time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );

		return true;
	}

	public static function get_currency_symbol( $currency = '' ) {
		if ( empty( $currency ) ) {
			$currency = self::get_selected_currency();
		}

		if ( 'base' === $currency ) {
			$currency = self::get_base_currency();
		}

		$symbols = get_woocommerce_currency_symbols();

		return $symbols[ $currency ] ?? '';
	}

	public static function format_price( $price, $args = array() ) {
		$currency = self::get_selected_currency();

		$args = wp_parse_args(
			$args,
			[ 'currency' => $currency, ]
		);

		return wc_price( $price, $args );
	}
}
