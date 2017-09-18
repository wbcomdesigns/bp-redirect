<?php
/**
 * @link              https://wbcomdesigns.com/contact/
 * @since             1.0.0
 * @package           BP_Redirect
 *
 * @wordpress-plugin
 * Plugin Name:       BP Redirect
 * Plugin URI:        https://wbcomdesigns.com/contact/
 * Description:       This plugin allows login and logout redirect according to the user role.
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
 * Constants used in the plugin
 *  @since   1.0.0
 *  @author  Wbcom Designs
 */
define( 'BP_REDIRECT_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'BP_REDIRECT_PLUGIN_URL', plugin_dir_url(__FILE__) );
define( 'BP_REDIRECT_DOMAIN','bp-redirect');
if (!defined('BP_ENABLE_MULTIBLOG')) {
    define('BP_ENABLE_MULTIBLOG', false);
}
if ( !defined( 'BP_ROOT_BLOG' ) ) {
    define( 'BP_ROOT_BLOG', 1 );
}

/**
 * Check plugin requirement on plugins loaded
 * this plugin requires BuddyPress to be installed and active
 */

add_action('plugins_loaded', 'bpr_plugin_init');
function bpr_plugin_init() {
    run_bp_redirect();
    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bpr_plugin_links' );
}

/**
 * Add the Plugin Links
 */
function bpr_plugin_links( $links ) {
    $bplock_links = array(
        '<a href="' . admin_url( 'admin.php?page=bp_redirect_settings' ) . '">' . __( 'Settings', BP_REDIRECT_DOMAIN ) . '</a>',
        '<a href="https://wbcomdesigns.com/contact/" target="_blank" title="' . __( 'Go for any custom development.', BP_REDIRECT_DOMAIN ) . '">' . __( 'Support', BP_REDIRECT_DOMAIN ) . '</a>'
    );
    return array_merge( $links, $bplock_links );
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
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bp_redirect() {
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-bp-redirect.php';
    $plugin = new BP_Redirect();
    $plugin->run();

}

function bpr_plugin_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta) {

    if (!is_plugin_active_for_network('buddypress/bp-loader.php')) {
        switch_to_blog($blog_id);
        //Buddypress Plugin is inactive, hence deactivate this plugin
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('The <b>BP Redirect</b> plugin requires <b>Buddypress</b> plugin to be installed and active. Return to <a href="' . admin_url('plugins.php') . '">Plugins</a>', BP_REDIRECT_DOMAIN));
        restore_current_blog();
    }
}
add_action('wpmu_new_blog', 'bpr_plugin_new_blog', 10, 6);