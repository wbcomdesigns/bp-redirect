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