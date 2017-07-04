<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Class to serve AJAX Calls
 */

if( !class_exists( 'BP_Redirect_AJAX' ) ) {
	class BP_Redirect_AJAX {

		/**
		* Constructor for BP Redirect ajax
		*  @since   1.0.0
		*  @author  Wbcom Designs
		*/
            
		function __construct() {
			
			add_action( 'wp_ajax_bp_redirect_admin_settings', array( $this, 'bp_redirect_save_admin_settings' ) );
			add_action( 'wp_ajax_nopriv_bp_redirect_admin_settings', array( $this, 'bp_redirect_save_admin_settings' ) );
		}

		/**
		*  Actions performed for saving admin settings
		*  @since   1.0.0
		*  @author  Wbcom Designs
		*/
                
		function bp_redirect_save_admin_settings() {
			if( $_POST['action'] === 'bp_redirect_admin_settings' ) {		
				parse_str($_POST['form'], $form_data);
				$setting_details = filter_var_array( $form_data, FILTER_SANITIZE_STRING );
				if( !empty( $setting_details) ){
					update_option('bp_redirect_admin_settings', $setting_details );
				}
			}	exit;
		}                
   
	}
	new BP_Redirect_AJAX();
}