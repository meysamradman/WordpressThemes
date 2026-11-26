<?php
/**
 * Liquid Themes Theme Framework
 * The Liquid_Updater initiate the theme engine
 */

if( !defined( 'ABSPATH' ) ) 
	exit; // Exit if accessed directly

#[AllowDynamicProperties]
class Liquid_Updater {
	
	private $remote_api_url;
	private $request_data;
	private $response_key;
	private $theme_slug;
	private $purchase_code;
	private $version;
	private $author;
	protected $strings = null;

	function __construct( $args = array(), $strings = array() ) {

		$args = wp_parse_args( $args, array(
			'remote_api_url' => 'https://api.liquid-themes.com/hub',
			'request_data' => array(),
			'theme_slug' => get_template(),
			'item_name' => '',
			'license' => '',
			'version' => '',
			'author' => ''
		) );
		extract( $args );

		$this->license = $license;
		$this->item_name = $item_name;
		$this->version = $version;
		$this->theme_slug = sanitize_key( $theme_slug );
		$this->remote_api_url = $remote_api_url;
		$this->response_key = $this->theme_slug . '-update-response';
		$this->strings = $strings;

		add_filter( 'site_transient_update_themes', array( &$this, 'theme_update_transient' ) );
		add_filter( 'delete_site_transient_update_themes', array( &$this, 'delete_theme_update_transient' ) );
		add_action( 'load-update-core.php', array( &$this, 'delete_theme_update_transient' ) );
		add_action( 'load-themes.php', array( &$this, 'delete_theme_update_transient' ) );
		add_action( 'load-themes.php', array( &$this, 'load_themes_screen' ) );

		add_action( 'admin_head', array( &$this, 'update_notice' ) );
		add_action( 'core_upgrade_preamble', array( &$this, 'updates_screen_card' ) );
		add_action( 'wp_ajax_lqd_update_notice_response', function() {
			if ( ! current_user_can( 'update_themes' ) || ! check_ajax_referer( 'lqd_update_notice_response', 'security', false ) ) {
				wp_send_json_error( 'Not allowed.', 403 );
			}
			set_transient( 'lqd_update_notice_response', [], 2 * WEEK_IN_SECONDS );
			wp_send_json_success('LIQUID - Update popup will appear after 2 weeks!');
		} );

	}

	function load_themes_screen() {
		add_thickbox();
		add_action( 'admin_notices', array( &$this, 'update_nag' ) );
	}

	function update_nag() {

		$strings = $this->strings;

		$theme = wp_get_theme( $this->theme_slug );

		$api_response = get_transient( $this->response_key );

		if ( false === $api_response ) {
			return;
		}

		$update_url = wp_nonce_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode( $this->theme_slug ), 'upgrade-theme_' . $this->theme_slug );
		$update_onclick = ' onclick="if ( confirm(\'' . esc_js( 'Updating this theme will lose any customizations you have made. \'Cancel\' to stop, \'OK\' to update.' ) . '\') ) {return true;}return false;"';

		if ( version_compare( $this->version, $api_response->new_version, '<' ) ) {

			echo '<div id="update-nag">';
			printf(
				wp_kses_post( __( '<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'hub' ) ),
				$theme->get( 'Name' ),
				esc_html( $api_response->new_version ),
				'#TB_inline?width=640&amp;inlineId=' . $this->theme_slug . '_changelog',
				$theme->get( 'Name' ),
				$update_url,
				$update_onclick
			);
			echo '</div>';
			if ( function_exists( 'liquid_promote_card' ) && current_user_can( 'update_themes' ) ) {
				echo '<div class="lqd-aa-updates" style="max-width:720px;">';
				echo liquid_promote_card( 'update' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the card.
				echo '</div>';
			}
			echo '<div id="' . $this->theme_slug . '_' . 'changelog" style="display:none;">';
			$sections  = isset( $api_response->sections ) ? (array) $api_response->sections : array();
			$changelog = ( isset( $sections['changelog'] ) && is_scalar( $sections['changelog'] ) ) ? (string) $sections['changelog'] : '';
			echo wp_kses_post( wpautop( $changelog ) );
			echo '</div>';
		}
	}

	/**
	 * Dashboard > Updates: the All-Access card under the list when this theme has an update waiting.
	 */
	function updates_screen_card() {
		if ( ! function_exists( 'liquid_promote_card' ) || ! current_user_can( 'update_themes' ) ) {
			return;
		}
		$api_response = get_transient( $this->response_key );
		if ( ! is_object( $api_response ) || empty( $api_response->new_version ) || ! version_compare( $this->version, (string) $api_response->new_version, '<' ) ) {
			return;
		}
		echo '<div class="lqd-aa-updates" style="max-width:720px;margin-top:24px;">';
		echo liquid_promote_card( 'update' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the card.
		echo '</div>';
	}

	function theme_update_transient( $value ) {
		$update_data = $this->check_for_update();
		if ( $update_data ) {

			if ( ! is_object( $value ) ) {
				$value = new stdClass;
			}

			$update_data['theme'] = $this->theme_slug;

			if ( version_compare( $this->version, $update_data['new_version'], '<' ) ) {
				$value->response[ $this->theme_slug ] = $update_data;
			} else {
				$value->no_update[ $this->theme_slug ] = $update_data;
			}
		}

		return $value;
	}

	function delete_theme_update_transient() {
		delete_transient( $this->response_key );
	}

	function check_for_update() {

		$update_data = get_transient( $this->response_key );

		if ( false === $update_data ) {
			$failed = false;

			$api_params = array(
				'action'  => 'get_version',
				'license' => $this->license,
				'slug' 	  => $this->theme_slug,
				'author' => $this->author
			);

			$response = wp_remote_get( $this->remote_api_url, array( 'timeout' => 15, 'body' => $api_params ) );

			// Make sure the response was successful
			if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
				$failed = true;
			}

			$update_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( ! is_object( $update_data ) ) {
				$failed = true;
			}

			// If the response failed, try again in 30 minutes
			if ( $failed ) {
				$data = new stdClass;
				$data->new_version = $this->version;
				set_transient( $this->response_key, $data, strtotime( '+30 minutes' ) );
				return false;
			}

			// If the status is 'ok', return the update arguments
			if ( ! $failed ) {
				if ( is_string( $update_data->sections ) && is_serialized( $update_data->sections ) ) {
					$update_data->sections = @unserialize( trim( $update_data->sections ), array( 'allowed_classes' => false ) );
				}
				set_transient( $this->response_key, $update_data, strtotime( '+12 hours' ) );
			}
		}

		if ( version_compare( $this->version, $update_data->new_version, '>=' ) ) {
			return false;
		}

		return (array) $update_data;
	}

	function update_notice() {

		global $pagenow;

		// check themes page
		if ( $pagenow === 'themes.php' ) {
			return;
		}

		// Only people who can update the theme see the popup.
		if ( ! current_user_can( 'update_themes' ) ) {
			return;
		}

		// limit request
		if ( get_transient( 'lqd_update_notice_response_cache' ) === 'cached' ) {
			return;
		}

		$check = get_transient( 'lqd_update_notice_response' );

		if ( false === $check ) {
			$failed = false;

			$api_params = array(
				'action'  => 'get_version',
				'license' => $this->license,
				'slug' 	  => $this->theme_slug,
				'author' => $this->author
			);

			$response = wp_remote_get( $this->remote_api_url, array( 'timeout' => 15, 'body' => $api_params ) );

			// Make sure the response was successful
			if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
				$failed = true;
			}

			if ( !$failed ) {

				$update_data = json_decode( wp_remote_retrieve_body( $response ) );
				set_transient( 'lqd_update_notice_response_cache', 'cached', 30 * MINUTE_IN_SECONDS );

				if ( is_object( $update_data ) && isset( $update_data->stable_version ) && is_scalar( $update_data->stable_version ) && version_compare( $this->version, (string) $update_data->stable_version, '<' ) ) {

					$features = ( isset( $update_data->features ) && is_scalar( $update_data->features ) ) ? (string) $update_data->features : '';

					// Text from the update server is escaped for HTML, then handed to the script as JSON.
					$popup = array(
						'title'    => sprintf(
							'<span class="dashicons dashicons-update-alt"></span> %s',
							esc_html__( 'Theme update available!', 'hub' )
						),
						'content'  => sprintf(
							'<p>Hub v%s %s</p><p>%s</p>',
							esc_html( (string) $update_data->stable_version ),
							esc_html__( 'is available!', 'hub' ),
							wp_kses_post( $features )
						) . ( function_exists( 'liquid_promote_card' ) ? liquid_promote_card( 'update' ) : '' ),
						'redirect' => esc_url_raw( admin_url( 'themes.php' ) ),
						'nonce'    => wp_create_nonce( 'lqd_update_notice_response' ),
					);

					wp_add_inline_script( 'liquid-admin', '
						jQuery( document ).ready(function() {
							var lqdUpdatePopup = ' . wp_json_encode( $popup ) . ';
							jQuery.confirm({
								columnClass: "lqd-update",
								type: "orange",
								title: lqdUpdatePopup.title,
								content: lqdUpdatePopup.content,
								buttons: {
									new: {
										text: "Later",
										action: function() {
											jQuery.post(ajaxurl, { "action": "lqd_update_notice_response", "security": lqdUpdatePopup.nonce }, function (response) {
												console.log(response.data);
											});
										}
									},
									confirm: {
										text: "Update now",
										action: function() {
											window.location.href = lqdUpdatePopup.redirect;
										}
									},
								}
							});
						});
					' );

				}

			}

		}

	}
	
}