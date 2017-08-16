<?php
/**
 * @link              https://wbcomdesigns.com/contact/
 * @since             1.0.0
 * @package           BP_Redirect
 *
 * @wordpress-plugin
 * Plugin Name:       BP Redirect
 * Plugin URI:        https://wbcomdesigns.com/contact/
 * Description:       This plugin allows login redirect according to user role.
 * Version:           1.0.0
 * Author:            Wbcom Designs <admin@wbcomdesigns.com>
 * Author URI:        https://wbcomdesigns.com/contact/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-redirect
 * Domain Path:       /languages
 *
 */
// If this file is called directly, abort.
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check plugin requirement on plugins loaded
 * this plugin requires BuddyPress to be installed and active
 */

add_action('plugins_loaded', 'bpr_plugin_init');
function bpr_plugin_init() {
    $bp_active = in_array('buddypress/bp-loader.php', get_option('active_plugins'));
    if ( current_user_can('activate_plugins') && $bp_active !== true ) {
        add_action('admin_notices', 'bpr_plugin_admin_notice');
    } 
}

/**
 *  Show admin notice when BuddyPress plugin not activated
 *  @since   1.0.0
 *  @author  Wbcom Designs
*/

function bpr_plugin_admin_notice() {
    $bpr_plugin = __( 'BP Redirect', BP_REDIRECT_DOMAIN );
    $bp_plugin = __( 'BuddyPress', BP_REDIRECT_DOMAIN );
    echo '<div class="error"><p>'
    . sprintf(__('The %1$s plugin requires %2$s plugin to activate and function correctly.', BP_REDIRECT_DOMAIN ), '<strong>' . esc_html($bpr_plugin) . '</strong>', '<strong>' . esc_html($bp_plugin) . '</strong>')
    . '</p></div>';
    deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bp-redirect-activator.php
 */

function activate_bp_redirect() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bp-redirect-activator.php';
	BP_Redirect_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bp-redirect-deactivator.php
 */

function deactivate_bp_redirect() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bp-redirect-deactivator.php';
	BP_Redirect_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bp_redirect' );
register_deactivation_hook( __FILE__, 'deactivate_bp_redirect' );

/**
 * Constants used in the plugin
 *  @since   1.0.0
 *  @author  Wbcom Designs
*/
define( 'BP_REDIRECT_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'BP_REDIRECT_PLUGIN_URL', plugin_dir_url(__FILE__) );
define( 'BP_REDIRECT_DOMAIN','bp-redirect');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bp-redirect.php';

 /**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bp_redirect() {

	$plugin = new BP_Redirect();
	$plugin->run();

}
run_bp_redirect();
