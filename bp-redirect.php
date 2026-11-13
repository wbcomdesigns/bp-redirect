<?php
/**
 * Login Logout Redirect plugin allows login and logout redirection based on user roles
 * with optional integration support for BuddyPress, WooCommerce, bbPress, Dokan,
 * LearnDash, and PeepSo.
 *
 * @link              https://wbcomdesigns.com/contact/
 * @since             1.0.0
 * @package           Wbcom_Redirect
 *
 * @wordpress-plugin
 * Plugin Name:       WB Login Logout Redirect
 * Plugin URI:        https://wbcomdesigns.com/downloads/buddypress-redirect/
 * Description:       Redirect users after login & logout by role — built for BuddyPress, BuddyBoss, WooCommerce, bbPress, Dokan, LearnDash & PeepSo.
 * Version:           2.3.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Wbcom Designs <admin@wbcomdesigns.com>
 * Author URI:        https://wbcomdesigns.com/contact/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-redirect
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WBCOM_REDIRECT_VERSION' ) ) {
	define( 'WBCOM_REDIRECT_VERSION', '2.3.0' );
}

if ( ! defined( 'WBCOM_REDIRECT_PLUGIN_PATH' ) ) {
	define( 'WBCOM_REDIRECT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WBCOM_REDIRECT_PLUGIN_FILE' ) ) {
	define( 'WBCOM_REDIRECT_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'WBCOM_REDIRECT_PLUGIN_URL' ) ) {
	define( 'WBCOM_REDIRECT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'WBCOM_REDIRECT_PLUGIN_BASENAME' ) ) {
	define( 'WBCOM_REDIRECT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

// Backward compat constants for wbcom admin wrapper.
if ( ! defined( 'BP_REDIRECT_PLUGIN_PATH' ) ) {
	define( 'BP_REDIRECT_PLUGIN_PATH', WBCOM_REDIRECT_PLUGIN_PATH );
}
if ( ! defined( 'BP_REDIRECT_PLUGIN_URL' ) ) {
	define( 'BP_REDIRECT_PLUGIN_URL', WBCOM_REDIRECT_PLUGIN_URL );
}
if ( ! defined( 'BP_REDIRECT_PLUGIN_FILE' ) ) {
	define( 'BP_REDIRECT_PLUGIN_FILE', WBCOM_REDIRECT_PLUGIN_FILE );
}

/**
 * Plugin activation.
 */
function wbcom_redirect_activate() {
	require_once WBCOM_REDIRECT_PLUGIN_PATH . 'includes/class-wbcom-redirect-activator.php';
	Wbcom_Redirect_Activator::activate();
}
register_activation_hook( __FILE__, 'wbcom_redirect_activate' );

/**
 * Plugin deactivation.
 */
function wbcom_redirect_deactivate() {
	// Stop the background preset-activation cron if it is still scheduled.
	if ( defined( 'WBCOM_REDIRECT_PRESET_CRON' ) ) {
		wp_clear_scheduled_hook( WBCOM_REDIRECT_PRESET_CRON );
	}
}
register_deactivation_hook( __FILE__, 'wbcom_redirect_deactivate' );

/**
 * Initialize plugin on plugins_loaded — no third-party dependency required.
 */
function wbcom_redirect_init() {
	require_once WBCOM_REDIRECT_PLUGIN_PATH . 'includes/class-wbcom-redirect.php';
	$plugin = new Wbcom_Redirect();
	$plugin->run();
}
add_action( 'plugins_loaded', 'wbcom_redirect_init' );

/**
 * Add settings link on plugins page.
 *
 * @param array $links Plugin action links.
 * @return array
 */
function wbcom_redirect_plugin_links( $links ) {
	$settings_link = '<a href="' . admin_url( 'admin.php?page=bp-redirect' ) . '">' . __( 'Settings', 'bp-redirect' ) . '</a>';
	$support_link  = '<a href="https://wbcomdesigns.com/contact/" target="_blank">' . __( 'Support', 'bp-redirect' ) . '</a>';
	array_unshift( $links, $settings_link, $support_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wbcom_redirect_plugin_links' );

/**
 * Redirect to settings page after activation.
 *
 * @param string $plugin Plugin file path.
 */
function wbcom_redirect_activation_redirect( $plugin ) {
	if ( plugin_basename( __FILE__ ) !== $plugin ) {
		return;
	}
	$action_param = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$plugin_param = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'activate' === $action_param && $plugin_param === $plugin ) {
		wp_safe_redirect( admin_url( 'admin.php?page=bp-redirect' ) );
		exit;
	}
}
add_action( 'activated_plugin', 'wbcom_redirect_activation_redirect' );

// Preset license key shipped with this free plugin (enables auto-updates).
if ( ! defined( 'WBCOM_REDIRECT_PRESET_KEY' ) ) {
	define( 'WBCOM_REDIRECT_PRESET_KEY', 'wbcomfreef2c8a1d7b3e94c6a0f5d2e9b7c1a4e33' );
}
if ( ! defined( 'WBCOM_REDIRECT_PRESET_ITEM_ID' ) ) {
	define( 'WBCOM_REDIRECT_PRESET_ITEM_ID', 80951 );
}
if ( ! defined( 'WBCOM_REDIRECT_PRESET_CRON' ) ) {
	define( 'WBCOM_REDIRECT_PRESET_CRON', 'wbcom_redirect_activate_preset_license' );
}

// EDD Software Licensing SDK — free plugin auto-updates with preset key.
add_action(
	'edd_sl_sdk_registry',
	function ( $registry ) {
		$registry->register(
			array(
				'id'      => 'bp-redirect',
				'url'     => 'https://wbcomdesigns.com',
				'item_id' => WBCOM_REDIRECT_PRESET_ITEM_ID,
				'version' => WBCOM_REDIRECT_VERSION,
				'file'    => WBCOM_REDIRECT_PLUGIN_FILE,
				'license' => WBCOM_REDIRECT_PRESET_KEY,
			)
		);
	}
);

if ( file_exists( WBCOM_REDIRECT_PLUGIN_PATH . 'vendor/easy-digital-downloads/edd-sl-sdk/edd-sl-sdk.php' ) ) {
	require_once WBCOM_REDIRECT_PLUGIN_PATH . 'vendor/easy-digital-downloads/edd-sl-sdk/edd-sl-sdk.php';
}

/**
 * Ensure the preset license key is stored and the background activation is
 * scheduled. Cheap and HTTP-free, so it is safe to run on every admin load.
 *
 * The remote activation itself is performed by a WP-Cron event (see
 * wbcom_redirect_activate_preset_license()), NOT here — a blocking
 * wp_remote_post() on admin_init previously made every admin page wait on
 * wbcomdesigns.com (up to the HTTP timeout) until the preset key activated.
 * WP-Cron runs in its own non-blocking request, so admin loads are never held.
 */
function wbcom_redirect_maybe_schedule_preset_activation() {
	if ( get_option( 'bp_redirect_preset_activated' ) ) {
		return;
	}

	// Store the preset key locally so the License screen reflects it immediately.
	if ( WBCOM_REDIRECT_PRESET_KEY !== get_option( 'bp_redirect_license_key' ) ) {
		update_option( 'bp_redirect_license_key', WBCOM_REDIRECT_PRESET_KEY, false );
	}

	// Defer the remote activation to the background; it retries on each cron
	// run until it succeeds, then unschedules itself.
	if ( ! wp_next_scheduled( WBCOM_REDIRECT_PRESET_CRON ) ) {
		wp_schedule_event( time(), 'twicedaily', WBCOM_REDIRECT_PRESET_CRON );
	}
}
add_action( 'admin_init', 'wbcom_redirect_maybe_schedule_preset_activation' );

/**
 * Background cron callback: perform the remote preset-license activation.
 *
 * Runs inside WP-Cron's own request, so the HTTP timeout never affects a
 * user-facing admin page. Writes exactly the same options the previous
 * inline activation wrote, then self-unschedules once activated.
 */
function wbcom_redirect_activate_preset_license() {
	if ( get_option( 'bp_redirect_preset_activated' ) ) {
		wp_clear_scheduled_hook( WBCOM_REDIRECT_PRESET_CRON );
		return;
	}

	if ( WBCOM_REDIRECT_PRESET_KEY !== get_option( 'bp_redirect_license_key' ) ) {
		update_option( 'bp_redirect_license_key', WBCOM_REDIRECT_PRESET_KEY, false );
	}

	$response = wp_remote_post(
		'https://wbcomdesigns.com',
		array(
			'timeout' => 15,
			'body'    => array(
				'edd_action' => 'activate_license',
				'license'    => WBCOM_REDIRECT_PRESET_KEY,
				'item_id'    => WBCOM_REDIRECT_PRESET_ITEM_ID,
				'url'        => home_url(),
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return; // Transient failure — the next scheduled run retries.
	}

	$data = json_decode( wp_remote_retrieve_body( $response ) );
	if ( isset( $data->success ) && $data->success ) {
		update_option( 'bp_redirect_preset_activated', 1, false );
		// Auto-enable usage tracking checkbox (unchanged data shape).
		update_option(
			'bp_redirect_license_key_allow_tracking',
			array(
				'allowed'   => true,
				'timestamp' => time(),
			),
			false
		);
		wp_clear_scheduled_hook( WBCOM_REDIRECT_PRESET_CRON );
	}
}
add_action( WBCOM_REDIRECT_PRESET_CRON, 'wbcom_redirect_activate_preset_license' );
