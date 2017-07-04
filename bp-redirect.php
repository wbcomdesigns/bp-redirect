<?php
/**
 * Plugin Name: BP Redirect
 * Plugin URI: https://wbcomdesigns.com/contact/
 * Description: This plugin allows buddypress login redirect according to user role.
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
        if ( !in_array('buddypress/bp-loader.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            add_action( 'admin_notices', 'bp_redirect_admin_notice' );            
        } else {
    		
       //     if( bp_is_active('groups' ) || bp_is_active('activity' ) || bp_is_active('xprofile' ) || bp_is_active('forums' ) || bp_is_active('messages' ) ) {
				/**
	            * Include needed files on init
	            */
	             $include_files = array(
		                'admin/settings.php', 
		                'includes/scripts.php',
		                'includes/ajax.php',
		                'includes/bp-redirect-login.php'
		            );
		            foreach($include_files as $include_file) {
		                include $include_file;
		            } 	           
        /**    } else {
        		add_action( 'admin_notices', 'bp_redirect_component_notice' );       
        	}            **/
    }

	 }


    /**
     * Show admin notice when buddypress not active or install
     *  @since   1.0.0
     *  @author  Wbcom Designs
    */
	
    function bp_redirect_admin_notice() {
        ?>
        <div class="error notice is-dismissible">
            <p><?php _e( 'The <b>BP Redirect</b> plugin requires <b>Buddypress</b> plugin to be installed and active.', BP_REDIRECT_DOMAIN ); ?></p>
        </div>
        <?php
    }
    
    /**
     * Show admin notice when buddypress user groups component not active
     *  @since   1.0.0
     *  @author  Wbcom Designs
    */   
	
    function bp_redirect_component_notice(){
        ?>
        <div class="error notice is-dismissible">
            <p><?php _e( 'The <b>BP Redirect</b> plugin requires <b>BuddyPress Components </b> to be active.', BP_REDIRECT_DOMAIN ); ?></p>
        </div>
        <?php  
    }