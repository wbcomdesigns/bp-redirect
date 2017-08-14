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
		*  @access public
		*/
                
		public function bp_redirect_save_admin_settings() {
			if( $_POST['action'] === 'bp_redirect_admin_settings' ) {		
				parse_str($_POST['login_details'], $login_form_data);
				parse_str($_POST['logout_details'], $logout_form_data);				
				$login_details = filter_var_array( $login_form_data, FILTER_SANITIZE_STRING );
				$logout_details = filter_var_array( $logout_form_data, FILTER_SANITIZE_STRING );
				$setting_arr = array_merge( $login_details, $logout_details );	
				if( !empty( $setting_arr) && !empty( $_POST['loginSequence'] )){					
					$setting_arr['loginSequence'] = $_POST['loginSequence'];
					$setting_arr['logoutSequence'] = $_POST['logoutSequence'];					
					update_option('bp_redirect_admin_settings', $setting_arr );			
				}
			}	exit;
		}             
   
	}
	new BP_Redirect_AJAX();
}