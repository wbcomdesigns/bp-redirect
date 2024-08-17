<?php

/**
 * BP Redirect Plugin allows login and logout redirects according to user roles.
 *
 * @link              https://wbcomdesigns.com/contact/
 * @since             1.0.0
 * @package           BP_Redirect
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Designs - BuddyPress Redirect
 * Plugin URI:        https://wbcomdesigns.com/contact/
 * Description:       This plugin allows login and logout redirects according to user roles.
 * Version:           1.8.3
 * Author:            Wbcom Designs <admin@wbcomdesigns.com>
 * Author URI:        https://wbcomdesigns.com/contact/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-redirect
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Constants used in the plugin
 *
 *  @since   1.0.0
 *  @package BP_Redirect
 */
define('REDIRECT_PLUGIN_VERSION', '1.8.3');
define('BP_REDIRECT_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BP_REDIRECT_PLUGIN_FILE', __FILE__);
define('BP_REDIRECT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BP_REDIRECT_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Check plugin requirements on plugins loaded.
 * This plugin requires BuddyPress to be installed and active.
 */
function bpr_plugin_init()
{
	if (class_exists('BuddyPress') && bpr_check_config()) {
		run_bp_redirect();
	} else {
		add_action('admin_notices', 'bpr_requires_buddypress');
	}
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'bpr_plugin_links');
}
add_action('plugins_loaded', 'bpr_plugin_init');

/**
 * BP Redirect checks the configuration.
 *
 * @return bool True if configuration is correct, false otherwise.
 */
function bpr_check_config()
{
	global $bp;
	$config = array(
		'blog_status'    => false,
		'network_active' => false,
		'network_status' => true,
	);

	if (get_current_blog_id() === bp_get_root_blog_id()) {
		$config['blog_status'] = true;
	}

	$network_plugins = get_site_option('active_sitewide_plugins', array());

	// No network plugins.
	if (empty($network_plugins)) {
		$config['network_status'] = false;
	}

	$config['network_active'] = isset($network_plugins[BP_REDIRECT_PLUGIN_BASENAME]);

	// If BuddyPress config is different than bp-activity plugin.
	if (! $config['blog_status'] || ! $config['network_status']) {
		if (! bp_core_do_network_admin() && ! $config['blog_status']) {
			add_action('admin_notices', 'bpr_same_blog_notice');
		}

		if (bp_core_do_network_admin() && ! $config['network_status']) {
			add_action('admin_notices', 'bpr_same_network_config_notice');
		}

		return false;
	}
	return true;
}

/**
 * Error Message for BP Redirect requires to be activated on the correct blog.
 *
 * @return void
 */
function bpr_same_blog_notice()
{
	echo '<div class="error"><p>'
		. esc_html__('BP Redirect must be activated on the blog where BuddyPress is activated.', 'bp-redirect')
		. '</p></div>';
}

/**
 * Error Message for network configuration.
 *
 * @return void
 */
function bpr_same_network_config_notice()
{
	echo '<div class="error"><p>'
		. esc_html__('BP Redirect and BuddyPress need to share the same network configuration.', 'bp-redirect')
		. '</p></div>';
}

/**
 * Add the Plugin Links
 *
 * @param array $links Plugin action links.
 * @return array Modified plugin action links.
 */
function bpr_plugin_links($links)
{
	$bpr_links = array(
		'<a href="' . esc_url(admin_url('admin.php?page=bp-redirect')) . '">' . esc_html__('Settings', 'bp-redirect') . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank" title="' . esc_attr__('Go for any custom development.', 'bp-redirect') . '">' . esc_html__('Support', 'bp-redirect') . '</a>',
	);
	return array_merge($links, $bpr_links);
}

/**
 * The code that runs during plugin activation.
 */
function activate_bp_redirect()
{
	require_once BP_REDIRECT_PLUGIN_PATH . 'includes/class-bp-redirect-activator.php';
	BP_Redirect_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_bp_redirect()
{
	require_once BP_REDIRECT_PLUGIN_PATH . 'includes/class-bp-redirect-deactivator.php';
	BP_Redirect_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_bp_redirect');
register_deactivation_hook(__FILE__, 'deactivate_bp_redirect');

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_bp_redirect()
{
	require BP_REDIRECT_PLUGIN_PATH . 'includes/class-bp-redirect.php';
	$plugin = new BP_Redirect();
	$plugin->run();

	// Initialize autoload for external dependencies.
	require_once __DIR__ . '/vendor/autoload.php';
	HardG\BuddyPress120URLPolyfills\Loader::init();
}

/**
 * Alert the admin if BuddyPress is not active.
 */
function bpr_requires_buddypress()
{
	echo '<div class="error"><p>';
	echo sprintf(
		/* translators: 1: BP Redirect, 2: BuddyPress */
		esc_html__('%1$s requires %2$s to be installed and active.', 'bp-redirect'),
		'<strong>' . esc_html__('BP Redirect', 'bp-redirect') . '</strong>',
		'<strong>' . esc_html__('BuddyPress', 'bp-redirect') . '</strong>'
	);
	echo '</p></div>';
}

/**
 * Redirect to plugin settings page after activation.
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 */
function bp_redirect_activation_redirect_settings($plugin)
{
	if (plugin_basename(__FILE__) === $plugin) {
		if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'activate' && isset($_REQUEST['plugin']) && $_REQUEST['plugin'] === $plugin) {
			wp_safe_redirect(admin_url('admin.php?page=bp-redirect&redirects=1'));
			exit;
		}
	}
}
add_action('activated_plugin', 'bp_redirect_activation_redirect_settings');