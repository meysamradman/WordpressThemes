<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Liquid_Shipping_Progress extends Liquid_Singleton {
	public static function is_active() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		if ( 'on' !== liquid_helper()->get_theme_option( 'wc-shipping-progress' ) ) {
			return false;
		}

		return true;
	}

	public static function is_area_active( $area = '' ) {
		$opt_areas = liquid_helper()->get_theme_option( 'wc-shipping-progress-areas' );

		if ( empty( $opt_areas ) ) {
			return false;
		}

		if ( empty( $area ) ) {
			return true;
		}

		return in_array( $area, $opt_areas );
	}

	public function init() {
		if ( ! self::is_active() ) {
			return;
		}

		add_action(
			'woocommerce_before_cart_table',
			function () {
				$this->render_with_wrapper( 'cart' );
			}
		);
	}

	public function render( $area = '' ) {
		if ( ! self::is_active() ) {
			return;
		}

		if ( ! self::is_area_active( $area ) ) {
			return;
		}

		$percent           = '';
		$limit             = 0;
		$total             = (float) WC()->cart->get_displayed_subtotal();
		$free              = false;
		$shipping_packages = WC()->cart->get_shipping_packages();
		$shipping_zone     = wc_get_shipping_zone( $shipping_packages[0] );

		foreach ( $shipping_zone->get_shipping_methods( true ) as $shipping_method ) {
			if ( 'free_shipping' === $shipping_method->id ) {
				$limit = ( float ) wc_format_decimal( $shipping_method->get_option( 'min_amount' ) );
			}
		}

		if ( empty( $limit ) ) {
			return;
		}

		if ( $total < $limit ) {
			$percent = floor( ( $total / $limit ) * 100 );
			$message = sprintf( /* translators: %s: price */ __( 'Add %s more to getting <strong>free shipping</strong>!', 'hub' ), wc_price( $limit - $total ) );
		} else {
			$message = esc_html__( 'You are getting free shipping!', 'hub' );
			$free    = true;
		}

		$classes = [ 'ld-shipping-progress' ];

		if ( $free ) {
			$classes[] = 'ld-shipping-progress--free';
			$percent   = 100;
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<span class="ld-shipping-progress__label"><?php echo wp_kses_post( $message ); ?></span>
			<div class="ld-shipping-progress__bar" style="--percent: <?php echo esc_attr( $percent ); ?>%;"></div>
		</div>
		<?php
	}

	public function render_with_wrapper( $area = [] ) {
		if ( ! self::is_active() ) {
			return;
		}

		if ( ! self::is_area_active( $area ) ) {
			return;
		}
		?>
		<div class="ld-shipping-progress-wrapper">
			<?php $this->render(); ?>
		</div>
		<?php
	}
}
