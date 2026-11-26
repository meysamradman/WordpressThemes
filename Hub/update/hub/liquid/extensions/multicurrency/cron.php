<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Liquid_Multicurrency_Cron extends Liquid_Singleton {
	public function init() {
		$this->set_cron();

		add_action( 'liquid_multicurrency_update_rates', [ Liquid_Multicurrency_Cron::instance(), 'set_rates' ] );
		add_action( 'redux/options/liquid_one_opt/saved', [ $this, 'api_source_change_event' ], 10, 2 );

		add_filter( 'cron_schedules', [ $this, 'add_custom_cron_intervals' ] );
	}

	public function api_source_change_event( $all_options, $changed_options ) {
		$value     = $all_options['wc-multicurrency-api-source'] ?? '';
		$old_value = $changed_options['wc-multicurrency-api-source'] ?? null;

		if ( is_null( $old_value ) || is_null( $value ) || $value === $old_value ) {
			return;
		}

		$this->set_rates();
	}

	public function set_cron() {
		if ( ! wp_next_scheduled( 'liquid_multicurrency_update_rates' ) ) {
			$source = liquid_helper()->get_option( 'wc-multicurrency-api-source' );

			if ( 'free' === $source ) {
				wp_schedule_event( time(), 'daily', 'liquid_multicurrency_update_rates' );
			}

			if ( 'fixerio' === $source ) {
				$interval = liquid_helper()->get_option( 'wc-multicurrency-cron-interval' );

				switch ( $interval ) {
					case '1min':
						wp_schedule_event( time(), 'liquid_every_minute', 'liquid_multicurrency_update_rates' );
						break;

					case '10mins':
						wp_schedule_event( time(), 'liquid_every_ten_minutes', 'liquid_multicurrency_update_rates' );
						break;

					case '1hour':
						wp_schedule_event( time(), 'hourly', 'liquid_multicurrency_update_rates' );
						break;

					default:
						wp_schedule_event( time(), 'daily', 'liquid_multicurrency_update_rates' );
						break;
				}
			}
		}

		if ( empty( Liquid_Multicurrency_DB::get_rates() ) ) {
			$this->set_rates();
		}
	}

	/**
	 * @throws Exception
	 */
	private function get_rates() {
		$api_source    = liquid_helper()->get_option( 'wc-multicurrency-api-source' );
		$api_source    = ! empty( $api_source ) ? $api_source : 'free';
		$base_currency = Liquid_Multicurrency_Rates::get_base_currency();
		$base_currency = wc_strtolower( $base_currency );

		if ( 'free' === $api_source ) {
			$req = wp_remote_get(
				sprintf(
					'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/%s.json',
					$base_currency
				),
			);

			if ( is_wp_error( $req ) ) {
				throw new Exception( 'Api error' );
			}

			$res = wp_remote_retrieve_body( $req );
			$res = json_decode( $res, true );

			if ( ! isset( $res[ $base_currency ] ) ) {
				throw new Exception( 'Rate not found' );
			}

			return $res[ $base_currency ];
		}

		if ( 'fixerio' === $api_source ) {
			$fixerio_api_key = liquid_helper()->get_option( 'fixerio-api-key' );

			$req = wp_remote_get(
				sprintf(
					'https://data.fixer.io/api/latest?base=%1$s&access_key=%2$s',
					$base_currency,
					$fixerio_api_key
				),
			);

			if ( is_wp_error( $req ) ) {
				throw new Exception( 'Api error' );
			}

			$res = wp_remote_retrieve_body( $req );
			$res = json_decode( $res, true );

			if ( empty( $res['success'] ) || ! isset( $res['rates'] ) ) {
				throw new Exception( 'Rate not found' );
			}

			return $res['rates'];
		}

		return [];
	}

	public function set_rates() {
		try {
			$rates = $this->get_rates();
		} catch ( Exception $e ) {
			Liquid_Multicurrency_DB::delete_rates();

			return;
		}

		Liquid_Multicurrency_DB::set_rates( $rates );
	}

	public function add_custom_cron_intervals( $schedules ) {
		if ( ! isset( $schedules['liquid_every_minute'] ) ) {
			$schedules['liquid_every_minute'] = [
				'interval' => 60,
				'display'  => 'Every Minute',
			];
		}

		if ( ! isset( $schedules['liquid_every_ten_minutes'] ) ) {
			$schedules['liquid_every_ten_minutes'] = [
				'interval' => 600,
				'display'  => 'Every 10 Minutes',
			];
		}

		return $schedules;
	}
}
