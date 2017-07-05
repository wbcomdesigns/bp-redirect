<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'BP_Redirect_Login' ) ) {
	class BP_Redirect_Login {

		/**
		* Constructor for BP Redirect login
		*  @since   1.0.0
		*  @author  Wbcom Designs
		*/
            
		function __construct() {
			
			add_filter( 'login_redirect', array( $this, 'bp_login_redirection_front') , 10, 3);

		}

		/**
		*  Actions performed after login
		*  @since   1.0.0
		*  @author  Wbcom Designs
		*  @access public
		*/
                
		public function bp_login_redirection_front( $redirect_to, $request, $user ) {
			global $bp;
		    global $wp_roles;
		   if ( ! is_wp_error( $user ) ) {
				$saved_setting = get_option('bp_redirect_admin_settings');
				$setting       = $saved_setting['bp_redirect_redirection'];
			
				$roles = $wp_roles->roles;  
				foreach( $roles as $key => $val ) {
					$current_user_role = $user->roles;
					if( $current_user_role[0] == $key ) {						
						if( !empty( $setting )) {
							$url = $this->redirect_according_settings( $key, $setting, $redirect_to, $request, $user );
							return $url;
						} else {	
							if ( isset( $user->roles ) && is_array( $user->roles ) ) {
								//check for admins
								if ( in_array( 'administrator', $user->roles ) ) {
									// redirect them to the default place
									return esc_url(admin_url());
								} else {
									return esc_url(home_url());
								}
							}			
							
						}
						
					}
				}	
			}		
		} 

		/**
	 	*  Redirects according plugin settings
		*
		*  @since 1.0.0
		*  @author  Wbcom Designs
		*  @access public
		*/

		public function redirect_according_settings(  $key, $setting, $redirect_to, $request, $user ){
			$login_type_val = '';
			$login_component = '';
			$login_url = '';
			if( array_key_exists( $key, $setting )) {
				$login_component = $setting[$key]['login_component'];											
				$login_url= $setting[$key]['login_url'];
			 	if( array_key_exists('login_type', $setting[$key])) {							 		
					$login_type_val = $setting[$key]['login_type'];	
					if( $login_type_val == 'referer' ){			
						if( isset( $login_component ) && $login_component == 'profile' ) {
							$redirect_url = $this->redirect_to_profile( $redirect_to, $request, $user );
			 				return esc_url( $redirect_url );
						} else {
							return esc_url( $login_component );	
						}			
					} else {				

						if( !empty( $login_url ) && $login_type_val == 'custom') {	
							return esc_url( $login_url );	
						} else {
							if ( isset( $user->roles ) && is_array( $user->roles ) ) {
								//check for admins
								if ( in_array( 'administrator', $user->roles ) ) {
									// redirect them to the default place
									return esc_url(admin_url());
								} else {
									return esc_url(home_url());
								}
							}	
						}

					}									
				}
				
			}
		}

		/**
	 	*  Redirects to profile page
		*
		*  @since 1.0.0
		*  @author  Wbcom Designs
		*  @access public
		*/

		public function redirect_to_profile( $redirect_to, $request, $user ){
			global $bp;
			return bp_core_get_user_domain($user->ID);
		}   
	}
	new BP_Redirect_Login();
}