<?php
/**
 * Liquid Themes Theme Framework
 * The All-Access promotion: one partial for every place the theme's wp-admin shows it (the Activation page, the
 * registered dashboard, the setup wizard's last step, the update popup and update notices, the one-time
 * "Updated to <version>" notice, and the menu item).
 *
 * The copy, links and artwork come from the theme's promotion JSON
 * (https://api.liquid-themes.com/wp-notification-api/<theme-slug>.json), cached for a day and revalidated every
 * ten minutes so a new version of the JSON appears without waiting for the cache to expire. If the JSON can't be
 * read, the built-in copy in liquid_promote_fallback() is shown instead.
 *
 * Admins only (manage_options; update_themes too for the update placements and the "Updated to" notice),
 * wp-admin only. The JSON is only ever read as data: every string is escaped and never rendered as HTML.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'liquid_promote_theme' ) ) {

	/**
	 * The one place the six themes differ: the theme's slug on the store (utm_medium, the JSON file name) and its name.
	 */
	function liquid_promote_theme() {
		return array(
			'slug' => 'hub',
			'name' => 'Hub',
		);
	}

	/**
	 * Whether the theme is registered, by the dashboard's own check (Liquid_Admin_Register::form()): a valid status,
	 * a purchase code and the Liquid Portal domain key. A valid status without the Portal sync still says
	 * "Action Required!", so it counts as activation.
	 */
	function liquid_promote_registered() {
		$theme = liquid_promote_theme();
		$slug  = $theme['slug'];
		return 'valid' === get_option( $slug . '_purchase_code_status', false )
			&& '' !== trim( (string) get_option( $slug . '_purchase_code', '' ) )
			&& '' !== trim( (string) get_option( $slug . '_purchase_code_domain_key', '' ) );
	}

	/** Who sees the promotion anywhere: site admins. */
	function liquid_promote_can_see() {
		return is_admin() && current_user_can( 'manage_options' );
	}

	/**
	 * Who gets the "Updated to" notice, and who may dismiss it: the same check on both sides. On multisite only super
	 * admins have update_themes, so a sub-site admin never sees a notice they can't close.
	 */
	function liquid_promote_can_dismiss() {
		return current_user_can( 'manage_options' ) && current_user_can( 'update_themes' );
	}

	/** The promotion JSON: one file per theme slug. */
	function liquid_promote_endpoint() {
		$theme = liquid_promote_theme();
		$url   = 'https://api.liquid-themes.com/wp-notification-api/' . rawurlencode( $theme['slug'] ) . '.json';

		/*
		 * The API is served through a CDN that can keep a copy for hours (hub.json currently says
		 * Cache-Control: max-age=14400), so a revalidation would keep reading the stale edge copy. A bucket
		 * that changes with the revalidation interval gives every site the same URL inside the window: the
		 * origin sees at most one request per interval, while a published version is never hidden behind it.
		 */
		$interval = liquid_promote_revalidate_interval();
		if ( $interval > 0 ) {
			$url = add_query_arg( 'v', (int) floor( time() / $interval ), $url );
		}

		return apply_filters( 'liquid_promote_endpoint', $url );
	}

	/** How often a cached payload is checked against the JSON, so a new version shows up without a day's wait. */
	function liquid_promote_revalidate_interval() {
		return (int) apply_filters( 'liquid_promote_revalidate', 10 * MINUTE_IN_SECONDS );
	}

	/**
	 * The built-in copy: everything the theme shows when the JSON is unreachable, invalid or not fetched yet. It is
	 * also the shape every renderer expects, so a partial JSON can fall back block by block.
	 */
	function liquid_promote_fallback() {
		$theme = liquid_promote_theme();
		$base  = 'https://liquid-themes.com/';

		return array(
			'schema'     => 1,
			'source'     => 'fallback',
			'version'    => '',
			'campaign'   => 'all-access-2026',
			'expires_at' => '',
			'utm'        => array(
				'source' => 'wp-admin',
				'medium' => $theme['slug'] . '-tf',
			),
			'card'       => array(
				'enabled' => true,
				'heading' => __( 'Introducing Liquid All-Access', 'hub' ),
				'body'    => array(
					array( 'text' => __( 'Access to entire Liquid Catalogue', 'hub' ) ),
					array( 'text' => __( '(all themes, plugins, demos and templates)', 'hub' ) ),
					array(
						'text'   => __( 'on unlimited websites.', 'hub' ),
						'strong' => true,
					),
				),
				'cta'     => array(
					'label' => __( 'Explore All Access', 'hub' ),
					'url'   => $base,
				),
				'image'   => array(
					'src'    => get_template_directory_uri() . '/liquid/assets/img/dashboard/all-access/aa-card-bg.png',
					'width'  => 490,
					'height' => 451,
					'alt'    => '',
				),
			),
			'notice'     => array(
				'enabled' => true,
				'title'   => __( 'Introducing All-Access with Unlimited Sites', 'hub' ),
				'points'  => array(
					__( 'Access to entire catalogue of LiquidThemes with a single key', 'hub' ),
					__( '6 themes, 25 plugins, 250+ demos and 2000+ templates', 'hub' ),
					__( 'Unlimited websites with a limited-time launch offer.', 'hub' ),
				),
				'cta'     => array(
					'label' => __( 'Explore All Access', 'hub' ),
					'url'   => $base,
				),
				'image'   => array(
					'src'    => get_template_directory_uri() . '/liquid/assets/img/dashboard/all-access/aa-notice-bg.png',
					'width'  => 1126,
					'height' => 454,
					'alt'    => '',
				),
			),
			'menu'       => array(
				'enabled'    => true,
				'label'      => __( 'Get All-Access', 'hub' ),
				'aria_label' => sprintf(
					/* translators: %s: theme name */
					__( 'All-Access by Liquid Themes, including %s', 'hub' ),
					$theme['name']
				),
				'url'        => $base,
			),
		);
	}

	/**
	 * Turn whatever the JSON returned into the shape the renderers expect. Anything that isn't schema 1 is refused so
	 * an old build keeps showing the built-in copy instead of half-reading a new document.
	 *
	 * @param mixed $json Decoded JSON.
	 * @return array|false
	 */
	function liquid_promote_normalize( $json ) {
		if ( ! is_array( $json ) || ! isset( $json['schema'] ) || 1 !== (int) $json['schema'] ) {
			return false;
		}

		$fallback = liquid_promote_fallback();

		$json['schema']     = 1;
		$json['source']     = 'live';
		$json['version']    = isset( $json['version'] ) ? (string) $json['version'] : '';
		$json['campaign']   = isset( $json['campaign'] ) ? (string) $json['campaign'] : '';
		$json['expires_at'] = isset( $json['expires_at'] ) ? (string) $json['expires_at'] : '';

		$utm         = isset( $json['utm'] ) && is_array( $json['utm'] ) ? $json['utm'] : array();
		$json['utm'] = array(
			'source' => isset( $utm['source'] ) ? sanitize_key( $utm['source'] ) : $fallback['utm']['source'],
			'medium' => isset( $utm['medium'] ) ? sanitize_key( $utm['medium'] ) : $fallback['utm']['medium'],
		);

		foreach ( array( 'card', 'notice', 'menu' ) as $block ) {
			if ( empty( $json[ $block ] ) || ! is_array( $json[ $block ] ) ) {
				$json[ $block ] = $fallback[ $block ];
				continue;
			}
			$json[ $block ]['enabled'] = isset( $json[ $block ]['enabled'] ) ? (bool) $json[ $block ]['enabled'] : true;
		}

		return $json;
	}

	/**
	 * One request to the promotion JSON.
	 *
	 * @param array|null $cached The payload already on hand, for a conditional request.
	 * @return array|false The normalized payload, or false on any failure.
	 */
	function liquid_promote_fetch( $cached = null ) {
		$args = array(
			'timeout'     => 3,
			'redirection' => 2,
		);
		if ( is_array( $cached ) && ! empty( $cached['etag'] ) ) {
			$args['headers'] = array( 'If-None-Match' => $cached['etag'] );
		}

		$response = wp_remote_get( liquid_promote_endpoint(), $args );
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( 304 === $code && is_array( $cached ) ) {
			return $cached;
		}
		if ( 200 !== $code ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$json = json_decode( ltrim( $body, "\xEF\xBB\xBF" ), true );
		$json = liquid_promote_normalize( $json );
		if ( false === $json ) {
			return false;
		}

		$etag = wp_remote_retrieve_header( $response, 'etag' );
		if ( is_string( $etag ) && '' !== $etag ) {
			$json['etag'] = $etag;
		}

		return $json;
	}

	/**
	 * The promotion payload. A day-long transient holds it; a ten-minute gate means most admin pages make no request,
	 * while a revalidation compares the JSON's version (or a 304) so published changes appear on the next page.
	 * Failures leave the last good payload alone, and with no payload at all the built-in copy is returned.
	 */
	function liquid_promote_data() {
		static $data = null;
		if ( null !== $data ) {
			return $data;
		}

		$theme = liquid_promote_theme();
		$key   = 'liquid_promote_' . $theme['slug'];
		$gate  = $key . '_gate';
		$fail  = $key . '_fail';

		$cached = get_transient( $key );

		if ( is_array( $cached ) ) {
			if ( false === get_transient( $gate ) ) {
				set_transient( $gate, 1, liquid_promote_revalidate_interval() );
				$fresh = liquid_promote_fetch( $cached );
				if ( is_array( $fresh ) ) {
					$cached = $fresh;
					set_transient( $key, $cached, DAY_IN_SECONDS );
					delete_transient( $fail );
				} else {
					set_transient( $gate, 1, 5 * MINUTE_IN_SECONDS );
				}
			}
			return $data = $cached;
		}

		if ( false !== get_transient( $fail ) ) {
			return $data = liquid_promote_fallback();
		}

		$fresh = liquid_promote_fetch();
		if ( is_array( $fresh ) ) {
			set_transient( $key, $fresh, DAY_IN_SECONDS );
			set_transient( $gate, 1, liquid_promote_revalidate_interval() );
			delete_transient( $fail );
			return $data = $fresh;
		}

		set_transient( $fail, 1, 15 * MINUTE_IN_SECONDS );
		set_transient( $gate, 1, 5 * MINUTE_IN_SECONDS );
		return $data = liquid_promote_fallback();
	}

	/**
	 * One block of the payload (card, notice or menu), or null when it is disabled or the campaign has expired.
	 * Expiry only hides the blocks: liquid_promote_data() keeps checking the JSON, so the next campaign takes over.
	 *
	 * @param string $name card, notice or menu.
	 * @return array|null
	 */
	function liquid_promote_block( $name ) {
		$data = liquid_promote_data();

		if ( ! empty( $data['expires_at'] ) ) {
			$expires = strtotime( $data['expires_at'] );
			if ( $expires && $expires < time() ) {
				return null;
			}
		}

		if ( empty( $data[ $name ] ) || ! is_array( $data[ $name ] ) || empty( $data[ $name ]['enabled'] ) ) {
			return null;
		}

		return $data[ $name ];
	}

	/**
	 * The store link, tagged like the store's own wp-admin links (src/lib/funnel/links.ts): utm_source=wp-admin,
	 * utm_medium=<theme>-tf for a ThemeForest build, utm_campaign=<place>. The JSON can change source and medium
	 * (root "utm", then a CTA's own "utm" on top); the campaign stays the place unless a CTA names one.
	 *
	 * @param string $place activation, registered, wizard, update, updated or menu.
	 * @param array  $cta   The block's CTA: 'url' and an optional 'utm'.
	 */
	function liquid_promote_url( $place, $cta = array() ) {
		$data  = liquid_promote_data();
		$theme = liquid_promote_theme();

		$url = isset( $cta['url'] ) && is_string( $cta['url'] ) ? $cta['url'] : '';
		$url = esc_url_raw( $url );
		if ( '' === $url ) {
			$url = 'https://liquid-themes.com/';
		}

		$utm = array(
			'source'   => 'wp-admin',
			'medium'   => $theme['slug'] . '-tf',
			'campaign' => sanitize_key( $place ),
		);

		if ( isset( $data['utm'] ) && is_array( $data['utm'] ) ) {
			$utm = wp_parse_args( array_filter( $data['utm'] ), $utm );
		}
		if ( isset( $cta['utm'] ) && is_array( $cta['utm'] ) ) {
			$utm = wp_parse_args( array_filter( $cta['utm'] ), $utm );
		}

		return add_query_arg(
			array(
				'utm_source'   => sanitize_key( $utm['source'] ),
				'utm_medium'   => sanitize_key( $utm['medium'] ),
				'utm_campaign' => sanitize_key( $utm['campaign'] ),
			),
			$url
		);
	}

	/** The card's styles, printed once per page before the first card. */
	function liquid_promote_styles() {
		static $printed = false;
		if ( $printed ) {
			return '';
		}
		$printed = true;
		ob_start();
		?>
<style id="lqd-aa-styles">
.lqd-aa{--aa-green:#00a32a;position:relative;isolation:isolate;overflow:hidden;container-type:inline-size;margin:0 0 24px;padding:32px;background:#fff;border-left:6px solid var(--aa-green);border-radius:2px;box-shadow:0 1px 2px rgba(0,0,0,.08);color:#3c434a;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;text-align:left}
.lqd-aa *,.lqd-aa *::before,.lqd-aa *::after{box-sizing:border-box}
.lqd-aa-body{position:relative;z-index:1;max-width:54%}
.lqd-aa h4.lqd-aa-head{margin:0 0 8px;padding:0;font-size:18px;font-weight:600;line-height:1.2;letter-spacing:-.01em;color:#3c434a}
.lqd-aa p.lqd-aa-line{margin:0 0 16px;font-size:14px;line-height:1.5;color:#3c434a}
.lqd-aa p.lqd-aa-line strong{font-weight:700;color:#1d2327}
.lqd-aa a.lqd-aa-btn{display:inline-block;padding:9px 20px;border-radius:999px;background:#1d1d1b;color:#fff;font-size:13px;font-weight:600;line-height:1.2;text-decoration:none;box-shadow:none}
.lqd-aa a.lqd-aa-btn:hover,.lqd-aa a.lqd-aa-btn:focus{background:#3a3a38;color:#fff}
.lqd-aa a.lqd-aa-btn:focus-visible{outline:2px solid #2271b1;outline-offset:2px}
.lqd-aa img.lqd-aa-art{position:absolute;z-index:0;top:50%;right:0;width:44%;height:auto;max-width:none;transform:translateY(-50%);pointer-events:none;user-select:none}
@container (max-width:420px){.lqd-aa-body{max-width:100%}.lqd-aa img.lqd-aa-art{display:none}}
.merlin__content .lqd-aa{margin:22px 0 0}
.jconfirm .lqd-aa{margin:12px 0 0}
</style>
		<?php
		return ob_get_clean();
	}

	/**
	 * The promotion card: the JSON's heading, lines and button, with the artwork on the right.
	 *
	 * @param string $place Where it's shown, for the link's utm_campaign.
	 * @return string Empty for anyone but an admin, when the JSON disables it, and after the first card on a page.
	 */
	function liquid_promote_card( $place ) {
		static $shown = false;
		if ( $shown || ! liquid_promote_can_see() ) {
			return '';
		}

		$block = liquid_promote_block( 'card' );
		if ( ! $block ) {
			return '';
		}
		$shown = true;

		$heading = isset( $block['heading'] ) ? (string) $block['heading'] : '';
		$cta     = isset( $block['cta'] ) && is_array( $block['cta'] ) ? $block['cta'] : array();
		$label   = isset( $cta['label'] ) ? (string) $cta['label'] : '';
		$image   = isset( $block['image'] ) && is_array( $block['image'] ) ? $block['image'] : array();
		$body    = isset( $block['body'] ) && is_array( $block['body'] ) ? $block['body'] : array();

		$lines = array();
		foreach ( $body as $line ) {
			if ( is_string( $line ) ) {
				$line = array( 'text' => $line );
			}
			if ( ! is_array( $line ) || ! isset( $line['text'] ) || ! is_scalar( $line['text'] ) ) {
				continue;
			}
			$text = esc_html( (string) $line['text'] );
			if ( ! empty( $line['strong'] ) ) {
				$text = '<strong>' . $text . '</strong>';
			}
			$lines[] = $text;
		}

		ob_start();
		echo liquid_promote_styles(); // phpcs:ignore WordPress.Security.EscapeOutput -- static CSS above.
		?>
<div class="lqd-aa">
	<div class="lqd-aa-body">
		<h4 class="lqd-aa-head"><?php echo esc_html( $heading ); ?></h4>
		<p class="lqd-aa-line"><?php echo implode( "<br>\n", $lines ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?></p>
		<a class="lqd-aa-btn" href="<?php echo esc_url( liquid_promote_url( $place, $cta ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $label ); ?></a>
	</div>
	<?php if ( ! empty( $image['src'] ) && ! empty( $image['width'] ) && ! empty( $image['height'] ) ) : ?>
	<img class="lqd-aa-art" src="<?php echo esc_url( $image['src'] ); ?>" alt="<?php echo esc_attr( isset( $image['alt'] ) ? $image['alt'] : '' ); ?>" width="<?php echo absint( $image['width'] ); ?>" height="<?php echo absint( $image['height'] ); ?>" decoding="async">
	<?php endif; ?>
</div>
		<?php
		return ob_get_clean();
	}

	/** The menu item's styles, printed once per page before the first item. */
	function liquid_promote_menu_styles() {
		static $printed = false;
		if ( $printed ) {
			return '';
		}
		$printed = true;
		ob_start();
		?>
<style id="lqd-aa-menu-styles">
.lqd-dsd-menu .lqd-aa-menu a{position:relative;color:#fff;background-image:linear-gradient(100deg,#2f43d9 0%,#6b45e0 46%,#ff6b82 100%);box-shadow:0 6px 16px -4px rgba(66,95,255,.45),inset 0 1px 0 rgba(255,255,255,.18)}
.lqd-dsd-menu .lqd-aa-menu a:hover,.lqd-dsd-menu .lqd-aa-menu a:focus{color:#fff;background-color:transparent;background-image:linear-gradient(100deg,#2839bd 0%,#5c37cc 46%,#f2556d 100%);box-shadow:0 8px 20px -4px rgba(66,95,255,.55),inset 0 1px 0 rgba(255,255,255,.24)}
.lqd-dsd-menu .lqd-aa-menu a::after{content:"";position:absolute;inset:0;border-radius:inherit;padding:2px;background:conic-gradient(from var(--lqd-aa-runner),rgba(255,255,255,0) 0deg,rgba(255,255,255,0) 285deg,#fff 322deg,rgba(255,255,255,0) 360deg);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);mask-composite:exclude;pointer-events:none;animation:lqd-aa-menu-runner 2s linear 3 forwards,lqd-aa-menu-runner-out .45s ease 6s forwards}
.lqd-dsd-menu .lqd-aa-menu a i{margin:0 0 0 8px;font-size:16px;color:inherit}
.lqd-dsd-menu .lqd-aa-menu a:focus-visible{outline:2px solid #1d2327;outline-offset:2px}
@property --lqd-aa-runner{syntax:"<angle>";inherits:false;initial-value:0deg}
@keyframes lqd-aa-menu-runner{to{--lqd-aa-runner:360deg}}
@keyframes lqd-aa-menu-runner-out{to{opacity:0}}
@media (prefers-reduced-motion:reduce){.lqd-dsd-menu .lqd-aa-menu a::after{animation:none;background:none}}
</style>
		<?php
		return ob_get_clean();
	}

	/**
	 * The promotion item in the theme's wp-admin menu (liquid-tabs.php): one link to the store, for admins. It is
	 * not the card, so it does not take the page's one-card-per-page slot.
	 *
	 * @return string Empty for anyone but an admin, or when the JSON disables the menu item.
	 */
	function liquid_promote_menu() {
		if ( ! liquid_promote_can_see() ) {
			return '';
		}

		$block = liquid_promote_block( 'menu' );
		if ( ! $block ) {
			return '';
		}

		$label = isset( $block['label'] ) ? (string) $block['label'] : '';
		$aria  = isset( $block['aria_label'] ) && '' !== (string) $block['aria_label'] ? (string) $block['aria_label'] : $label;
		$url   = isset( $block['url'] ) ? $block['url'] : '';

		ob_start();
		echo liquid_promote_menu_styles(); // phpcs:ignore WordPress.Security.EscapeOutput -- static CSS above.
		?>
<li class="lqd-aa-menu">
	<a href="<?php echo esc_url( liquid_promote_url( 'menu', array( 'url' => $url ) ) ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $aria ); ?>">
		<span><?php echo esc_html( $label ); ?></span>
		<i class="lqd-icn-ess icon-md-arrow-forward" aria-hidden="true"></i>
	</a>
</li>
		<?php
		return ob_get_clean();
	}

	/** The "Updated to" notice's styles, printed once per page before the notice. */
	function liquid_promote_notice_styles() {
		static $printed = false;
		if ( $printed ) {
			return '';
		}
		$printed = true;
		ob_start();
		?>
<style id="lqd-aa-notice-styles">
.lqd-aa-notice,.lqd-aa-notice *{box-sizing:border-box}
.lqd-aa-notice{position:relative;padding:18px 24px;padding-right:min(58%,680px);background:#fff;overflow:hidden;border-radius:2px;}
.lqd-aa-notice-body{position:relative;z-index:1;max-width:640px}
.lqd-aa-notice .lqd-aa-notice-kicker{margin:0 0 6px;font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:#646970}
.lqd-aa-notice h2.lqd-aa-notice-title{margin:0 0 10px;padding:0;font-size:21px;font-weight:600;line-height:1.25;letter-spacing:-.01em;color:#1d2327}
.lqd-aa-notice .lqd-aa-notice-points{margin:0 0 16px;padding:0 0 0 18px;list-style:disc}
.lqd-aa-notice .lqd-aa-notice-points li{margin:0 0 2px;padding:0;font-size:13px;line-height:1.45;color:#3c434a}
.lqd-aa-notice .lqd-aa-notice-points li::marker{color:#1d2327}
.lqd-aa-notice a.lqd-aa-notice-btn{display:inline-block;padding:9px 20px;border-radius:999px;background:#1d1d1b;color:#fff;font-size:13px;font-weight:600;line-height:1.2;text-decoration:none;box-shadow:none}
.lqd-aa-notice a.lqd-aa-notice-btn:hover,.lqd-aa-notice a.lqd-aa-notice-btn:focus{background:#3a3a38;color:#fff}
.lqd-aa-notice a.lqd-aa-notice-btn:focus-visible{outline:2px solid #2271b1;outline-offset:2px}
.lqd-aa-notice .lqd-aa-notice-art{position:absolute;z-index:0;top:50%;right:0;width:min(58%,680px);height:auto;max-width:none;transform:translateY(-50%);pointer-events:none;user-select:none}
.lqd-aa-notice .lqd-aa-dismiss{position:absolute;z-index:2;top:10px;right:12px;display:flex;align-items:center;justify-content:center;width:32px;height:32px;padding:0;border-radius:50%;background:#1d1d1b;color:#fff;font-size:16px;line-height:1;text-decoration:none}
.lqd-aa-notice .lqd-aa-dismiss:hover,.lqd-aa-notice .lqd-aa-dismiss:focus{background:#3a3a38;color:#fff}
.lqd-aa-notice .lqd-aa-dismiss:focus-visible{outline:2px solid #2271b1;outline-offset:2px}
.toplevel_page_liquid .lqd-aa-notice {margin:40px 4% 4%}
.toplevel_page_liquid .lqd-aa-notice, .liquid_page_liquid-reset .lqd-aa-notice, .liquid_page_liquid-about .lqd-aa-notice, .liquid_page_liquid-reset .lqd-aa-notice {margin:40px 3% 3%}
@media (max-width:782px){.lqd-aa-notice{padding:16px}.lqd-aa-notice .lqd-aa-notice-art{display:none}.lqd-aa-notice-body{max-width:none}}
</style>
		<?php
		return ob_get_clean();
	}

	/*
	 * After an update: a one-time "Updated to <version>" notice, for admins, on the next wp-admin
	 * page after the theme's version changes. It stays until dismissed, and once dismissed it stays dismissed
	 * for that theme version and promotion version. A new promotion version (any change to the JSON's "version")
	 * brings it back, so published corrections and new campaigns are seen. A fresh install records its version
	 * without a notice. A site with no recorded version that already holds this theme's data
	 * (liquid_promote_had_theme()) updated from a build without this notice, so it gets the notice for this version.
	 */

	/** The option holding the last theme version an admin page saw, and the one holding a version owed a notice. */
	function liquid_promote_option( $what ) {
		$theme = liquid_promote_theme();
		return 'liquid_' . str_replace( '-', '_', $theme['slug'] ) . '_aa_' . $what;
	}

	/** The parent theme's version, as its style.css says. */
	function liquid_promote_version() {
		return (string) wp_get_theme( get_template() )->get( 'Version' );
	}

	/**
	 * Whether this site ran the theme before any version was recorded, read from data only an earlier build leaves
	 * behind (a fresh install has none of it on its first wp-admin page): the theme's registration options, the
	 * setup wizard's done-or-skipped flag, saved Liquid theme options, or theme mods other than the ones WordPress
	 * writes itself on a theme switch.
	 */
	function liquid_promote_had_theme() {
		$theme = liquid_promote_theme();
		$keys  = array(
			$theme['slug'] . '_purchase_code',
			$theme['slug'] . '_purchase_code_status',
			'merlin_' . strtolower( preg_replace( '#[^a-zA-Z]#', '', get_template() ) ) . '_completed',
		);
		if ( function_exists( 'liquid' ) && is_object( liquid() ) && method_exists( liquid(), 'get_option_name' ) && liquid()->get_option_name() ) {
			$keys[] = liquid()->get_option_name();
		}
		foreach ( $keys as $key ) {
			if ( false !== get_option( $key, false ) ) {
				return true;
			}
		}
		$mods = get_theme_mods();
		if ( is_array( $mods ) ) {
			foreach ( array_keys( $mods ) as $key ) {
				if ( is_string( $key ) && ! in_array( $key, array( 'nav_menu_locations', 'sidebars_widgets', 'custom_css_post_id' ), true ) ) {
					return true;
				}
			}
		}
		return false;
	}

	function liquid_promote_track_version() {
		if ( wp_doing_ajax() || ! liquid_promote_can_dismiss() ) {
			return;
		}
		$current = liquid_promote_version();
		$seen    = get_option( liquid_promote_option( 'version' ), '' );
		if ( '' === $current || $seen === $current ) {
			return;
		}
		update_option( liquid_promote_option( 'version' ), $current, false );
		// A change of version owes this one notice. With no version recorded, a fresh install records it only,
		// and a site that already had the theme updated from a build without this notice.
		if ( '' !== $seen || liquid_promote_had_theme() ) {
			update_option( liquid_promote_option( 'updated' ), $current, false );
		}
	}
	add_action( 'admin_init', 'liquid_promote_track_version' );

	/**
	 * Whether the notice should be shown for this theme version and promotion version. The dismiss record holds the
	 * theme version, the promotion version and the campaign: a new theme version or a new promotion version shows
	 * the notice again. With the JSON unreachable, the record is left alone so a fallback payload can't revive a
	 * notice the admin already closed.
	 *
	 * @param string $theme_version The version the notice says was installed.
	 * @param array  $data          The promotion payload.
	 */
	function liquid_promote_notice_is_new( $theme_version, $data ) {
		$seen = get_option( liquid_promote_option( 'seen' ), array() );
		if ( ! is_array( $seen ) || empty( $seen ) ) {
			return true;
		}

		if ( ! isset( $seen['theme'] ) || (string) $seen['theme'] !== (string) $theme_version ) {
			return true;
		}

		if ( ! isset( $data['source'] ) || 'live' !== $data['source'] ) {
			return false;
		}

		$version  = isset( $data['version'] ) ? (string) $data['version'] : '';
		$campaign = isset( $data['campaign'] ) ? (string) $data['campaign'] : '';

		// A payload without a version can't prove it is new: leave the dismissal alone rather than revive the notice.
		if ( '' === $version ) {
			return false;
		}

		return ! isset( $seen['version'], $seen['campaign'] )
			|| (string) $seen['version'] !== $version
			|| (string) $seen['campaign'] !== $campaign;
	}

	function liquid_promote_notice() {
		if ( ! liquid_promote_can_see() || ! liquid_promote_can_dismiss() ) {
			return;
		}
		$owed = get_option( liquid_promote_option( 'updated' ), '' );
		if ( '' === $owed || liquid_promote_version() !== $owed ) {
			return;
		}

		$block = liquid_promote_block( 'notice' );
		if ( ! $block ) {
			return;
		}

		$data = liquid_promote_data();
		if ( ! liquid_promote_notice_is_new( $owed, $data ) ) {
			return;
		}

		$theme   = liquid_promote_theme();
		$dismiss = wp_nonce_url(
			add_query_arg(
				array(
					'action'  => 'liquid_promote_dismiss',
					'version' => $owed,
				),
				admin_url( 'admin-post.php' )
			),
			'liquid_promote_dismiss'
		);

		$title  = isset( $block['title'] ) ? (string) $block['title'] : '';
		$points = isset( $block['points'] ) && is_array( $block['points'] ) ? $block['points'] : array();
		$cta    = isset( $block['cta'] ) && is_array( $block['cta'] ) ? $block['cta'] : array();
		$label  = isset( $cta['label'] ) ? (string) $cta['label'] : '';
		$image  = isset( $block['image'] ) && is_array( $block['image'] ) ? $block['image'] : array();
		?>
<div class="notice notice-success lqd-aa-notice">
	<?php echo liquid_promote_notice_styles(); // phpcs:ignore WordPress.Security.EscapeOutput -- static CSS above. ?>
	<a class="lqd-aa-dismiss" href="<?php echo esc_url( $dismiss ); ?>" aria-label="<?php esc_attr_e( 'Dismiss', 'hub' ); ?>">&times;</a>
	<div class="lqd-aa-notice-body">
		<p class="lqd-aa-notice-kicker"><?php echo esc_html( sprintf( /* translators: 1: theme name, 2: version */ __( 'Updated to %1$s %2$s', 'hub' ), $theme['name'], $owed ) ); ?></p>
		<h2 class="lqd-aa-notice-title"><?php echo esc_html( $title ); ?></h2>
		<ul class="lqd-aa-notice-points">
			<?php foreach ( $points as $point ) : ?>
				<?php if ( is_scalar( $point ) && '' !== (string) $point ) : ?>
			<li><?php echo esc_html( (string) $point ); ?></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<a class="lqd-aa-notice-btn" href="<?php echo esc_url( liquid_promote_url( 'updated', $cta ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $label ); ?></a>
	</div>
	<?php if ( ! empty( $image['src'] ) && ! empty( $image['width'] ) && ! empty( $image['height'] ) ) : ?>
	<img class="lqd-aa-notice-art" src="<?php echo esc_url( $image['src'] ); ?>" alt="<?php echo esc_attr( isset( $image['alt'] ) ? $image['alt'] : '' ); ?>" width="<?php echo absint( $image['width'] ); ?>" height="<?php echo absint( $image['height'] ); ?>" decoding="async">
	<?php endif; ?>
</div>
		<?php
	}
	add_action( 'admin_notices', 'liquid_promote_notice' );

	function liquid_promote_dismiss() {
		if ( ! liquid_promote_can_dismiss() ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'hub' ), 403 );
		}
		check_admin_referer( 'liquid_promote_dismiss' );
		$owed = get_option( liquid_promote_option( 'updated' ), '' );
		if ( isset( $_GET['version'] ) && sanitize_text_field( wp_unslash( $_GET['version'] ) ) === $owed ) {
			$data = liquid_promote_data();
			update_option(
				liquid_promote_option( 'seen' ),
				array(
					'theme'    => (string) $owed,
					'version'  => isset( $data['source'] ) && 'live' === $data['source'] && isset( $data['version'] ) ? (string) $data['version'] : '',
					'campaign' => isset( $data['campaign'] ) ? (string) $data['campaign'] : '',
				),
				false
			);
		}
		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
		exit;
	}
	add_action( 'admin_post_liquid_promote_dismiss', 'liquid_promote_dismiss' );
}
