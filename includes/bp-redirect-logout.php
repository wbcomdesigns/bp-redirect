<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'BP_Redirect_logout' ) ) {
	class BP_Redirect_logout {

		/**
		* Constructor for BP Redirect logout
		*  @since   1.0.0
		*  @author  Wbcom Designs
		*/
            
		function __construct() {
			
			add_filter( 'logout_redirect', array( $this, 'bp_logout_redirection_front') , 10, 3);

		}

		/**
		*  Actions performed after logout
		*  @since   1.0.0
		*  @author  Wbcom Designs
		*  @access public
		*/
                
		public function bp_logout_redirection_front( $redirect_to, $request, $user ) {
			global $bp;
		    global $wp_roles;
		    if ( ! is_wp_error( $user ) ) {
				$saved_setting = get_option('bp_redirect_admin_settings');
				$setting       = $saved_setting['bp_logout_redirect_settings'];		
				$roles = $wp_roles->roles;  
				foreach( $roles as $key => $val ) {
					$current_user_role = $user->roles;
					if( $current_user_role[0] == $key ) {						
						if( !empty( $setting )) {
							$url = $this->bpr_logout_redirect_according_settings( $key, $setting, $redirect_to, $request, $user );
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

		public function bpr_logout_redirect_according_settings(  $key, $setting, $redirect_to, $request, $user ){
			$logout_type_val = '';
			$logout_component = '';
			$logout_url = '';
			if( array_key_exists( $key, $setting )) {
				if ( in_array('buddypress/bp-loader.php', apply_filters('active_plugins', get_option('active_plugins')))) { 
					$logout_component = $setting[$key]['logout_component'];
				} 
				$logout_url= $setting[$key]['logout_url'];
			 	if( array_key_exists('logout_type', $setting[$key])) {							 		
					$logout_type_val = $setting[$key]['logout_type'];	
					if( $logout_type_val == 'referer' ) {		
						if( !empty( $logout_component ) && $logout_component == 'profile' ) {
							$redirect_url = $this->bpr_logout_redirect_to_member_profile( $redirect_to, $request, $user );
			 				return esc_url( $redirect_url );
						} elseif( !empty( $logout_component ) && $logout_component == 'member_activity' ) {
							$redirect_url = $this->bpr_logout_redirect_to_member_activity( $redirect_to, $request, $user );
			 				return esc_url( $redirect_url );								
						} else {
							$url = $this->bpr_logout_redirect_general( $user );
							return $url;							
						}		
					} else {
						if( !empty( $logout_url ) && $logout_type_val == 'custom' ) {
							return esc_url( $logout_url );	
						} else {
							$url = $this->bpr_logout_redirect_general( $user ); 
							return $url;
						}
					}									
				} else {
					$url = $this->bpr_logout_redirect_general( $user ); 
					return $url;
				}
				
			}
		}

		/**
	 	*  Redirects to Member's profile page
		*
		*  @since 1.0.0
		*  @author  Wbcom Designs
		*  @access public
		*/

		public function bpr_logout_redirect_to_member_profile( $redirect_to, $request, $user ){
			global $bp;
			$url = bp_core_get_user_domain($user->ID).'profile/';
			return $url;
		}

		/**
	 	*  Redirects to Member's activity page
		*
		*  @since 1.0.0
		*  @author  Wbcom Designs
		*  @access public
		*/

		public function bpr_logout_redirect_to_member_activity( $redirect_to, $request, $user ){
			global $bp;
			$url = bp_core_get_user_domain($user->ID).'activity/';
			return $url;
		}

		/**
	 	*  Redirect normal redirect 
		*
		*  @since 1.0.0
		*  @author  Wbcom Designs
		*  @access public
		*/

		public function bpr_logout_redirect_general( $user ){
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
	new BP_Redirect_logout();
}