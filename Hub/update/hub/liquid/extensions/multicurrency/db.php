<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Liquid_Multicurrency_DB {
	private static string $opt_name = 'liquid_multicurrency_rates';

	public static function set_rates( $data ) {
		update_option( self::$opt_name, $data );
	}

	public static function delete_rates() {
		delete_option( self::$opt_name );
	}

	public static function get_rates() {
		$rates = get_option( self::$opt_name );

		if ( ! $rates ) {
			Liquid_Multicurrency_Cron::instance()->set_rates();

			return get_option( self::$opt_name );
		}

		return $rates;
	}

	public static function get_rate( $currency ) {
		$lowercase_currency = wc_strtolower( $currency );
		$upper_currency     = wc_strtoupper( $currency );
		$rates              = self::get_rates();

		if ( isset( $rates[ $lowercase_currency ] ) ) {
			return $rates[ $lowercase_currency ];
		} elseif ( isset( $rates[ $upper_currency ] ) ) {
			return $rates[ $upper_currency ];
		}

		return 1;
	}

	public static function get_enabled_currencies() {
		$data = liquid_helper()->get_option( 'wc-enabled-multi-currencies' );

		return ! empty( $data ) ? $data : [ Liquid_Multicurrency_Rates::get_base_currency() ];
	}
}
