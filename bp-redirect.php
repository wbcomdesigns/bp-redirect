<?php
/**
 * BP Redirect Plugin allows login and logout redirection based on user roles.
 *
 * @link              https://wbcomdesigns.com/contact/
 * @since             1.0.0
 * @package           BP_Redirect
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Designs - BuddyPress Redirect
 * Plugin URI:        https://wbcomdesigns.com/contact/
 * Description:       This plugin allows login and logout redirection based on user roles.
 * Version:           1.9.0
 * Author:            Wbcom Designs <admin@wbcomdesigns.com>
 * Author URI:        https://wbcomdesigns.com/contact/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-redirect
 * Domain Path:       /languages
 */

// Abort if this file is called directly.
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Define constants used in the plugin.
 *
 * @since   1.0.0
 * @author  Wbcom Designs
 */

if (! defined('REDIRECT_PLUGIN_VERSION')) {
	define('REDIRECT_PLUGIN_VERSION', '1.9.0');
}

if (! defined('BP_REDIRECT_PLUGIN_PATH')) {
	define('BP_REDIRECT_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

if (! defined('BP_REDIRECT_PLUGIN_FILE')) {
	define('BP_REDIRECT_PLUGIN_FILE', __FILE__);
}

if (! defined('BP_REDIRECT_PLUGIN_URL')) {
	define('BP_REDIRECT_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (! defined('BP_REDIRECT_PLUGIN_BASENAME')) {
	define('BP_REDIRECT_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

require BP_REDIRECT_PLUGIN_PATH . 'plugin-update-checker/plugin-update-checker.php';
/**
 * Initialize the plugin on plugins loaded.
 * This plugin requires BuddyPress to be installed and active.
 */
function bp_redirect_plugin_init()
{
	if (class_exists('BuddyPress')) {
		if (bp_redirect_check_config()) {
			bp_redirect_start_execution();
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'bp_redirect_plugin_links');
		}
	} else {
		if( ! function_exists( 'deactivate_plugins' ) ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'bp_redirect_plugin_links');
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'bp_redirect_required_plugin_admin_notice' );
	}
}
add_action('wp_loaded', 'bp_redirect_plugin_init');

/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author wbcomdesigns
 * @since  2.3.0
 */
function bp_redirect_required_plugin_admin_notice() {
	$bpmb_plugin = esc_html__( 'BuddyPress Redirect', 'bp-redirect' );
	$bp_plugin   = esc_html__( 'BuddyPress', 'bp-redirect' );
	echo '<div class="error"><p>';
	// translators: %1$s is replaced with the BuddyPress Member Blog and %2$s is replaced with the BuddyPress.
	echo sprintf( esc_html__( '%1$s is ineffective now as it requires %2$s to be installed and active.', 'bp-redirect' ), '<strong>' . esc_html( $bpmb_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>' );
	echo '</p></div>';
}
/**
 * Check BP Redirect configuration.
 */
function bp_redirect_check_config() {
	global $bp;
	$config = array(
		'blog_status'    => false,
		'network_active' => false,
		'network_status' => true,
	);
	if ( get_current_blog_id() === bp_get_root_blog_id() ) {
		$config['blog_status'] = true;
	}

	$network_plugins = get_site_option( 'active_sitewide_plugins', array() );

	// No Network plugins.
	if (empty($network_plugins)) {
		$check[] = $bp->basename;
	}

	$check[] = BP_REDIRECT_PLUGIN_BASENAME;

	// Check if the plugins are network activated.
	$network_active = array_diff($check, array_keys($network_plugins));

	// If the result is 1, your plugin is network activated
	// and not BuddyPress or vice versa. Configuration is not okay.
	if (count($network_active) === 1) {
		$config['network_status'] = false;
	}

	// Determine if the plugin is network activated to display the appropriate
	// notice (admin or network admin) for warning messages.
	$config['network_active'] = isset($network_plugins[BP_REDIRECT_PLUGIN_BASENAME]);

	// If BuddyPress config differs from the bp-activity plugin.
	if (! $config['blog_status'] || ! $config['network_status']) {

		$warnings = array();
		if (! bp_core_do_network_admin() && ! $config['blog_status']) {
			add_action('admin_notices', 'bp_redirect_same_blog');
			$warnings[] = __('BP Redirect must be activated on the blog where BuddyPress is active.', 'bp-redirect');
		}

		if (bp_core_do_network_admin() && ! $config['network_status']) {
			add_action('admin_notices', 'bp_redirect_same_network_config');
			$warnings[] = __('BP Redirect and BuddyPress must share the same network configuration.', 'bp-redirect');
		}

		if (! empty($warnings)) {
			return false;
		}
	}

	return true;
}

/**
 * Display error message if BP Redirect must be activated on the same blog as BuddyPress.
 *
 * @return void
 */
function bp_redirect_same_blog()
{
	echo '<div class="error"><p>'
		. esc_html(__('BP Redirect must be activated on the blog where BuddyPress is active.', 'bp-redirect'))
		. '</p></div>';
}

/**
 * Display error message if BP Redirect and BuddyPress do not share the same network configuration.
 *
 * @return void
 */
function bp_redirect_same_network_config()
{
	echo '<div class="error"><p>'
		. esc_html(__('BP Redirect and BuddyPress must share the same network configuration.', 'bp-redirect'))
		. '</p></div>';
}

/**
 * Add plugin action links.
 *
 * @param string $links Plugin action links.
 */
function bp_redirect_plugin_links($links)
{
	$bpr_links = array(
		'<a href="' . admin_url('admin.php?page=bp-redirect') . '">' . __('Settings', 'bp-redirect') . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank" title="' . __('Need custom development?', 'bp-redirect') . '">' . __('Support', 'bp-redirect') . '</a>',
	);
	return array_merge($links, $bpr_links);
}


/**
 * Runs the plugin during activation.
 */
function bp_redirect_activate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-bp-redirect-activator.php';
	BP_Redirect_Activator::activate();
}

/**
 * Runs the plugin during deactivation.
 */
function bp_redirect_deactivate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-bp-redirect-deactivator.php';
	BP_Redirect_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'bp_redirect_activate' );
register_deactivation_hook( __FILE__, 'bp_redirect_deactivate' );

/**
 * Execute the core functionality of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * initiating the plugin from this point does not affect the page lifecycle.
 *
 * @since    1.0.0
 */
function bp_redirect_start_execution()
{
	require plugin_dir_path(__FILE__) . 'includes/class-bp-redirect.php';
	$plugin = new BP_Redirect();
	$plugin->run();
}

/**
 * Redirect to the plugin settings page after activation.
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 */
function bp_redirect_activation_redirect_settings($plugin)
{
	if (plugin_basename(__FILE__) === $plugin && class_exists( 'BuddyPress' )) {
		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'activate' && isset($_REQUEST['plugin']) && $_REQUEST['plugin'] == $plugin) { //phpcs:ignore
			wp_safe_redirect(admin_url('admin.php?page=bp-redirect'));
			exit;
		}
	}
}
add_action('activated_plugin', 'bp_redirect_activation_redirect_settings');
