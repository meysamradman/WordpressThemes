<?php
/** Vendor update discovery, independent of activation and frontend rendering. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Liquid_Updater {
	const UPSTREAM_VERSION = '6.0.3';
	const CRON_HOOK = 'hub_check_theme_updates';
	private $theme_slug;
	private $version;
	private $response_key;
	private $attempt_key;
	private $remote_api_url = 'https://api.liquid-themes.com/hub';

	public function __construct( $args = array() ) {
		$this->theme_slug = isset( $args['theme_slug'] ) ? sanitize_key( $args['theme_slug'] ) : 'hub';
		$this->version = isset( $args['version'] ) ? $args['version'] : self::UPSTREAM_VERSION;
		$this->response_key = $this->theme_slug . '-update-response';
		$this->attempt_key = $this->theme_slug . '-update-last-attempt';
		add_action( 'init', array( $this, 'schedule' ) );
		add_action( self::CRON_HOOK, array( $this, 'refresh' ) );
		add_action( 'load-update-core.php', array( $this, 'updates_screen' ) );
		add_filter( 'site_transient_update_themes', array( $this, 'theme_update_transient' ) );
	}

	public function schedule() {
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time() + 30, 'twicedaily', self::CRON_HOOK );
		}
	}

	public function updates_screen() {
		if ( ! current_user_can( 'update_themes' ) ) {
			return;
		}
		$force = isset( $_GET['force-check'], $_GET['_wpnonce'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'upgrade-core' );
		$this->refresh( (bool) $force );
	}

	/** Only cron and the dedicated Updates screen call this method. */
	public function refresh( $force = false ) {
		if ( ! $force && false !== get_transient( $this->attempt_key ) ) {
			return;
		}
		// Back off failures without erasing the last successful response.
		set_transient( $this->attempt_key, 1, 30 * MINUTE_IN_SECONDS );
		$url = add_query_arg( array(
			'action' => 'get_version', 'license' => '',
			'slug' => $this->theme_slug, 'author' => 'Liquid Themes',
		), $this->remote_api_url );
		$response = wp_remote_get( $url, array( 'timeout' => 5, 'limit_response_size' => 262144 ) );
		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return;
		}
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $data ) || empty( $data['new_version'] ) || ! is_string( $data['new_version'] ) ||
			! preg_match( '/^\d+(?:\.\d+){1,3}(?:[-+][a-zA-Z0-9.-]+)?$/', $data['new_version'] ) ) {
			return;
		}
		$update = array(
			'theme' => $this->theme_slug,
			'new_version' => $data['new_version'],
			'url' => isset( $data['url'] ) && is_string( $data['url'] ) ? esc_url_raw( $data['url'] ) : '',
			'package' => isset( $data['package'] ) && is_string( $data['package'] ) ? esc_url_raw( $data['package'] ) : '',
		);
		set_transient( $this->response_key, $update, 7 * DAY_IN_SECONDS );
		set_transient( $this->attempt_key, 1, 12 * HOUR_IN_SECONDS );
	}

	/** Reading badges never performs HTTP, including on frontend pages. */
	public function theme_update_transient( $value ) {
		$data = get_transient( $this->response_key );
		if ( ! is_array( $data ) || empty( $data['new_version'] ) ) {
			return $value;
		}
		if ( ! is_object( $value ) ) {
			$value = new stdClass();
		}
		if ( version_compare( $this->version, $data['new_version'], '<' ) ) {
			$value->response[ $this->theme_slug ] = $data;
			unset( $value->no_update[ $this->theme_slug ] );
		} else {
			$value->no_update[ $this->theme_slug ] = $data;
			unset( $value->response[ $this->theme_slug ] );
		}
		return $value;
	}
}
