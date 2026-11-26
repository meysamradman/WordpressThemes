<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Liquid_Multicurrency extends Liquid_Singleton {
	public function init() {
		require_once get_template_directory() . '/liquid/extensions/multicurrency/ajax.php';
		require_once get_template_directory() . '/liquid/extensions/multicurrency/cart.php';
		require_once get_template_directory() . '/liquid/extensions/multicurrency/cron.php';
		require_once get_template_directory() . '/liquid/extensions/multicurrency/db.php';
		require_once get_template_directory() . '/liquid/extensions/multicurrency/rates.php';

		Liquid_Multicurrency_Rates::instance()->init();
		Liquid_Multicurrency_Cron::instance()->init();
		Liquid_Multicurrency_Ajax::instance()->init();
		Liquid_Multicurrency_Cart::instance()->init();
	}

	public static function is_active() {
		return class_exists( 'WooCommerce' ) &&
		       class_exists( 'Liquid_Multicurrency_DB' ) &&
		       ! empty( \Liquid_Multicurrency_DB::get_rates() );
	}
}

add_action(
	'init',
	function () {
		if ( 'on' === liquid_helper()->get_option( 'wc-enable-multicurrency' ) ) {
			Liquid_Multicurrency::instance()->init();
		}
	}
);
