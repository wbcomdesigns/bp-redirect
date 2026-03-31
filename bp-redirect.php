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
 * Plugin Name:       Wbcom Designs - Login Logout Redirect
 * Plugin URI:        https://wbcomdesigns.com/downloads/buddypress-redirect/
 * Description:       Redirect users after login and logout based on user roles, with optional support for BuddyPress, WooCommerce, bbPress, Dokan, LearnDash, and PeepSo.
 * Version:           2.1.0
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
	define( 'WBCOM_REDIRECT_VERSION', '2.1.0' );
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
	// Nothing to clean up.
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
