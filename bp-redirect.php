<?php
/**
 * Plugin Name: BP Redirect
 * Plugin URI: https://wbcomdesigns.com/contact/
 * Description: This plugin allows login redirect according to user role.
 * Version: 1.0.0
 * Author: Wbcom Designs
 * Author URI: http://wbcomdesigns.com
 * License: GPLv2+
 * Text Domain: bp-redirect
 * Domain Path: /languages
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	/**
	 * Constants used in the plugin
	 *  @since   1.0.0
	 *  @author  Wbcom Designs
	*/
	define( 'BP_REDIRECT_PLUGIN_PATH', plugin_dir_path(__FILE__) );
	define( 'BP_REDIRECT_PLUGIN_URL', plugin_dir_url(__FILE__) );
	define( 'BP_REDIRECT_DOMAIN','bp-redirect');


	/**
	 * Plugin Activation
	 *  @since   1.0.0
	 *  @author  Wbcom Designs
	*/

	register_activation_hook( __FILE__, 'bp_redirect_plugin_activation' );
	function bp_redirect_plugin_activation() {
	        //Check if "Buddypress" plugin is active or not
	        if (!in_array('buddypress/bp-loader.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	                //Buddypress Plugin is inactive, hence deactivate this plugin
	                deactivate_plugins( plugin_basename( __FILE__ ) );

	        }
	}

	/**
	 * Include needed files on init
	 *  @since   1.0.0
	 *  @author  Wbcom Designs
	*/
	add_action( 'plugins_loaded', 'bp_redirect_plugins_files' );

	function bp_redirect_plugins_files() {
	    global $bp;
	    $include_files = array(
	            'admin/settings.php', 
	            'includes/scripts.php',
	            'includes/ajax.php',
	            'includes/bp-redirect-login.php'
	        );
        foreach($include_files as $include_file) {
            include $include_file;
        }            

	 }