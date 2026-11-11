<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Liquid_Multicurrency_Ajax extends Liquid_Singleton {
	public function init() {
		add_action( 'wp_ajax_liquid_multicurrency_switch_currency', [ $this, 'change_currency' ] );
		add_action( 'wp_ajax_nopriv_liquid_multicurrency_switch_currency', [ $this, 'change_currency' ] );
	}

	public function change_currency() {
		check_ajax_referer( 'liquidWooNonce' );

		$currency = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : '';

		$set = \Liquid_Multicurrency_Rates::set_currency( $currency );

		if ( ! $set ) {
			wp_send_json_error();
		}

		wp_send_json_success();
	}
}
