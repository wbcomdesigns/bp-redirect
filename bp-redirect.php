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
if (!defined('BP_REDIRECT_PLUGIN_BASENAME')) {
    define('BP_REDIRECT_PLUGIN_BASENAME', plugin_basename(__FILE__));
}
/**
 * Check plugin requirement on plugins loaded
 * this plugin requires BuddyPress to be installed and active
 */

add_action('bp_loaded', 'bpr_plugin_init');
function bpr_plugin_init() {
    if ( bpr_check_config() ){
        run_bp_redirect();
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bpr_plugin_links' );
    }
}

function bpr_check_config(){
    global $bp;
    $config = array(
        'blog_status'    => false, 
        'network_active' => false, 
        'network_status' => true 
    );
    if ( get_current_blog_id() == bp_get_root_blog_id() ) {
        $config['blog_status'] = true;
    }
    
    $network_plugins = get_site_option( 'active_sitewide_plugins', array() );

    // No Network plugins
    if ( empty( $network_plugins ) )

    // Looking for BuddyPress and bp-activity plugin
    $check[] = $bp->basename;
    $check[] = BP_REDIRECT_PLUGIN_BASENAME;

    // Are they active on the network ?
    $network_active = array_diff( $check, array_keys( $network_plugins ) );
    
    // If result is 1, your plugin is network activated
    // and not BuddyPress or vice & versa. Config is not ok
    if ( count( $network_active ) == 1 )
        $config['network_status'] = false;

    // We need to know if the plugin is network activated to choose the right
    // notice ( admin or network_admin ) to display the warning message.
    $config['network_active'] = isset( $network_plugins[ BP_REDIRECT_PLUGIN_BASENAME ] );

    // if BuddyPress config is different than bp-activity plugin
    if ( !$config['blog_status'] || !$config['network_status'] ) {

        $warnings = array();
        if ( !bp_core_do_network_admin() && !$config['blog_status'] ) {
            add_action( 'admin_notices', 'bpr_same_blog' );
            $warnings[] = __( 'BP Redirect requires to be activated on the blog where BuddyPress is activated.', 'bp-redirect' );
        }

        if ( bp_core_do_network_admin() && !$config['network_status'] ) {
            add_action( 'admin_notices', 'bpr_same_network_config' );
            $warnings[] = __( 'BP Redirect and BuddyPress need to share the same network configuration.', 'bp-redirect' );
        }

        if ( ! empty( $warnings ) ) :
            return false;
        endif;
    }
    return true;
}

function bpr_same_blog(){
    echo '<div class="error"><p>'
    . esc_html( __( 'BP Redirect requires to be activated on the blog where BuddyPress is activated.', 'bp-redirect' ) )
    . '</p></div>';
}

function bpr_same_network_config(){
    echo '<div class="error"><p>'
    . esc_html( __( 'BP Redirect and BuddyPress need to share the same network configuration.', 'bp-redirect' ) )
    . '</p></div>';
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

/**
 *  Check if buddypress activate.
 */
function bpr_requires_buddypress()
{

    if ( !class_exists( 'Buddypress' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        //deactivate_plugins('buddypress-polls/buddypress-polls.php');
        add_action( 'admin_notices', 'bpr_required_plugin_admin_notice' );
        unset($_GET['activate']);
    }
}

add_action( 'admin_init', 'bpr_requires_buddypress' );
/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author wbcomdesigns
 * @since  1.2.0
 */
function bpr_required_plugin_admin_notice()
{

    $bpquotes_plugin          = esc_html__('BP Redirect', 'bp-redirect');
    $bp_plugin                = esc_html__('BuddyPress', 'bp-redirect');
    echo '<div class="error"><p>';
    echo sprintf(esc_html__('%1$s is ineffective now as it requires %2$s to be installed and active.', 'bp-redirect'), '<strong>' . esc_html($bpquotes_plugin) . '</strong>', '<strong>' . esc_html($bp_plugin) . '</strong>');
    echo '</p></div>';
    if (isset($_GET['activate']) ) {
        unset($_GET['activate']);
    }
}