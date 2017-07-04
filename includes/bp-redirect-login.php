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
		*/
                
		function bp_login_redirection_front( $redirect_to, $request, $user ) {
			global $bp;
		    global $wp_roles;
		   
			$saved_setting = get_option('bp_redirect_admin_settings');
			$setting       = $saved_setting['bp_redirect_redirection'];
			$roles = $wp_roles->roles;  
			foreach( $roles as $key => $val ) {
				$current_user_role = $user->roles;
				if( $current_user_role[0] == $key ) {					
					$login_type_val = '';
					$login_component = '';
					$login_url = '';
					if( !empty( $setting )) {
						if( array_key_exists( $key, $setting )) {
						 	if( array_key_exists('login_type', $setting[$key])){
								$login_type_val = $setting[$key]['login_type'];										
							}
							$login_component = $setting[$key]['login_component'];											
							$login_url= $setting[$key]['login_url'];
						}	
					}

					if( $login_type_val == 'referer' ){						
						switch( $login_component ) {
							case 'groups':	 $redirect_url = $this->redirect_to_groups( $redirect_to, $request, $user );
											 return $redirect_url;	
											 break;
							case 'members':	 $redirect_url = $this->redirect_to_members( $redirect_to, $request, $user);
											 return $redirect_url;	
											 break;
							case 'activity': $redirect_url = $this->redirect_to_activity( $redirect_to, $request, $user );
							 				 return $redirect_url;	
											 break;		
						 	case 'profile':  $redirect_url = $this->redirect_to_profile( $redirect_to, $request, $user );
							 				 return $redirect_url;
											 break;						
						}
					} else {

					}
				}
			}	
		} 


		function redirect_to_groups( $redirect_to, $request, $user ){
	 	 	global $bp;
			$activity_slug = bp_get_activity_root_slug();
			$redirect_url = $bp->root_domain."/".$activity_slug;
			return $redirect_url;
		}   

		function redirect_to_members( $redirect_to, $request, $user ){
			global $bp;
			return bp_core_get_user_domain($user->ID);
		}   

		function redirect_to_profile( $redirect_to, $request, $user ){
			global $bp;
			return bp_core_get_user_domain($user->ID);
		}   

		function redirect_to_activity( $redirect_to, $request, $user ){
			global $bp;
			$activity_slug = bp_get_activity_root_slug();
			$redirect_url = $bp->root_domain."/".$activity_slug;
			return $redirect_url;
		}       
   
	}
	new BP_Redirect_Login();
}