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
 * Version:           1.8.3
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
	define('REDIRECT_PLUGIN_VERSION', '1.8.3');
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

/**
 * Initialize the plugin on plugins loaded.
 * This plugin requires BuddyPress to be installed and active.
 */
function bpr_plugin_init()
{
	if (class_exists('BuddyPress')) {
		if (bpr_check_config()) {
			run_bp_redirect();
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'bpr_plugin_links');
		}
	} else {
		run_bp_redirect();
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'bpr_plugin_links');
	}
}
add_action('wp_loaded', 'bpr_plugin_init');

/**
 * Check BP Redirect configuration.
 */
function bpr_check_config() {
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
			add_action('admin_notices', 'bpr_same_blog');
			$warnings[] = __('BP Redirect must be activated on the blog where BuddyPress is active.', 'bp-redirect');
		}

		if (bp_core_do_network_admin() && ! $config['network_status']) {
			add_action('admin_notices', 'bpr_same_network_config');
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
function bpr_same_blog()
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
function bpr_same_network_config()
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
function bpr_plugin_links($links)
{
	$bpr_links = array(
		'<a href="' . admin_url('admin.php?page=bp-redirect') . '">' . __('Settings', 'bp-redirect') . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank" title="' . __('Need custom development?', 'bp-redirect') . '">' . __('Support', 'bp-redirect') . '</a>',
	);
	return array_merge( $links, $bplock_links );
}

/**
 * Runs the plugin during activation.
 */
function activate_bp_redirect()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-bp-redirect-activator.php';
	BP_Redirect_Activator::activate();
}

/**
 * Runs the plugin during deactivation.
 */
function deactivate_bp_redirect()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-bp-redirect-deactivator.php';
	BP_Redirect_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bp_redirect' );
register_deactivation_hook( __FILE__, 'deactivate_bp_redirect' );

/**
 * Execute the core functionality of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * initiating the plugin from this point does not affect the page lifecycle.
 *
 * @since    1.0.0
 */
function run_bp_redirect()
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

	if (plugin_basename(__FILE__) === $plugin) {
		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'activate' && isset($_REQUEST['plugin']) && $_REQUEST['plugin'] == $plugin) { //phpcs:ignore
			wp_safe_redirect(admin_url('admin.php?page=bp-redirect&redirects=1'));
			exit;
		}
	}
}
add_action('activated_plugin', 'bp_redirect_activation_redirect_settings');
