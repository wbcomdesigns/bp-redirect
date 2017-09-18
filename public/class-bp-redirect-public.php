<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/contact/
 * @since      1.0.0
 *
 * @package    BP_Redirect
 * @subpackage BP_Redirect/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 *
 * @package    BP_Redirect
 * @subpackage BP_Redirect/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class BP_Redirect_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
	}

	/**
	*  Actions performed after login
	*  @since   1.0.0
	*  @author  Wbcom Designs
	*  @access public
	*/
	public function bp_login_redirection_front( $redirect_to, $request = '', $user = '' ) {
	    global $wp_roles;
	    if ( ! is_wp_error( $user ) && !empty( $user )) {
			$saved_setting = get_option('bp_redirect_admin_settings');
			$setting       = $saved_setting['bp_login_redirect_settings'];

			foreach( $wp_roles->roles as $key => $val ) {
				if( in_array( $key, $user->roles ) ) {
					if( !empty( $setting )) {
						$url = $this->bpr_login_redirect_according_settings( $key, $setting, $redirect_to, $request, $user );
					} else {	
						$url = $this->bpr_redirect_general( $user );
					}
				}
			}

			$url_headers = get_headers( $url, 1 );
			if( stripos( $url_headers[0], '404' ) ) {
				$url = get_home_url();
			}
			return $url;
		}		
	} 

	/**
 	*  Login redirects according plugin settings
	*
	*  @since 1.0.0
	*  @author  Wbcom Designs <admin@wbcomdesigns.com>
	*  @access public
	*/

	public function bpr_login_redirect_according_settings(  $key, $setting, $redirect_to, $request, $user ) {
		$login_type_val = '';
		$login_component = '';
		$login_url = '';
		if( array_key_exists( $key, $setting )) {

			if (is_multisite()) {
				 // Makes sure the plugin is defined before trying to use it
				if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
					require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
				}
				if ( is_plugin_active_for_network( 'buddypress/bp-loader.php' ) === true ) {
					$login_component = $setting[$key]['login_component'];
				}
			} elseif ( in_array('buddypress/bp-loader.php', apply_filters('active_plugins', get_option('active_plugins')))) { 
				$login_component = $setting[$key]['login_component'];
			}

			$login_url= $setting[$key]['login_url'];

		 	if( array_key_exists('login_type', $setting[$key])) {							 		
				$login_type_val = $setting[$key]['login_type'];	
				if( $login_type_val == 'referer' ) {		
					$url = $this->bp_login_redirect_referer( $login_component, $redirect_to, $request, $user );
					return $url;
				} else {
					if( !empty( $login_url ) && $login_type_val == 'custom' ) {
						return esc_url( $login_url );	
					} else {
						$url = $this->bpr_redirect_general( $user ); 
						return $url;
					}
				}									
			} else {
				$url = $this->bpr_redirect_general( $user ); 
				return $url;
			}	
			
		}
	}

	public function bp_login_redirect_referer( $login_component, $redirect_to, $request, $user ) {
		if( !empty( $login_component ) && $login_component == 'profile' ) {
			$redirect_url = $this->bpr_login_redirect_to_profile( $redirect_to, $request, $user );
				return esc_url( $redirect_url );
		} elseif( !empty( $login_component ) && $login_component == 'member_activity'  ) {
			$redirect_url = $this->bpr_login_redirect_to_member_activity( $redirect_to, $request, $user );
				return esc_url( $redirect_url );					
		} elseif( !empty( $login_component ) ) {
			return esc_url( $login_component );							
		} else {
			$url = $this->bpr_redirect_general( $user );
			return $url;									
		}	
	}
	/**
 	*  Login redirects to Member's profile page
	*
	*  @since 1.0.0
	*  @author  Wbcom Designs <admin@wbcomdesigns.com>
	*  @access public
	*/

	public function bpr_login_redirect_to_profile( $redirect_to, $request, $user ){
		$url = bp_core_get_user_domain($user->ID).'profile/';
		return $url;
	}

	/**
 	*  Login redirects to Member's activity page
	*
	*  @since 1.0.0
	*  @author  Wbcom Designs <admin@wbcomdesigns.com>
	*  @access public
	*/

	public function bpr_login_redirect_to_member_activity( $redirect_to, $request, $user ){
		$url = bp_core_get_user_domain($user->ID).'activity/';
		return $url;
	}

	/**
 	*  Default redirect when no redirect url found
	*
	*  @since 1.0.0
	*  @author  Wbcom Designs <admin@wbcomdesigns.com>
	*  @access public
	*/

	public function bpr_redirect_general( $user ){
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

	/**
	*  Actions performed after logout
	*  @since   1.0.0
	*  @author Wbcom Designs <admin@wbcomdesigns.com>
	*  @access public
	*/
            
	public function bp_logout_redirection_front( $redirect_to, $request='', $user='' ) {
	    global $wp_roles;
	    if ( ! is_wp_error( $user ) && !empty( $user ) ) {
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
 	*  Logout redirects according plugin settings
	*
	*  @since 1.0.0
	*  @author  Wbcom Designs <admin@wbcomdesigns.com>
	*  @access public
	*/

	public function bpr_logout_redirect_according_settings(  $key, $setting, $redirect_to, $request, $user ){
		$logout_type_val = '';
		$logout_component = '';
		$logout_url = '';
		if( array_key_exists( $key, $setting ) ) {
			if ( in_array('buddypress/bp-loader.php', apply_filters('active_plugins', get_option('active_plugins')))) { 
				$logout_component = $setting[$key]['logout_component'];
			} 
			$logout_url= $setting[$key]['logout_url'];
		 	if( array_key_exists('logout_type', $setting[$key]) ) {							 		
				$logout_type_val = $setting[$key]['logout_type'];	
				if( $logout_type_val == 'referer' ) {
					$url = $this->bp_logout_redirect_referer( $logout_component, $redirect_to, $request, $user );
					return $url;
				} else {
					if( !empty( $logout_url ) && $logout_type_val == 'custom' ) {
						return esc_url( $logout_url );	
					} else {
						$url = $this->bpr_redirect_general( $user ); 
						return $url;
					}
				}									
			} else {
				$url = $this->bpr_redirect_general( $user ); 
				return $url;
			}
			
		}
	}

	/**
 	*  Logout redirects when logout redirect type referer
	*
	*  @since 1.0.0
	*  @author  Wbcom Designs <admin@wbcomdesigns.com>
	*  @access public
	*/

	public function bp_logout_redirect_referer( $logout_component, $redirect_to, $request, $user ) {
		if( !empty( $logout_component ) && $logout_component == 'profile' ) {
			$redirect_url = $this->bpr_logout_redirect_to_member_profile( $redirect_to, $request, $user );
				return esc_url( $redirect_url );
		} elseif( !empty( $logout_component ) && $logout_component == 'member_activity' ) {
			$redirect_url = $this->bpr_logout_redirect_to_member_activity( $redirect_to, $request, $user );
				return esc_url( $redirect_url );								
		} elseif( !empty( $logout_component ) ) {
			return esc_url( $logout_component );
		} else {
			$url = $this->bpr_redirect_general( $user );
			return $url;							
		}	
	}

	/**
 	*  Logout redirects to Member's profile page
	*
	*  @since 1.0.0
	*  @author  Wbcom Designs <admin@wbcomdesigns.com>
	*  @access public
	*/

	public function bpr_logout_redirect_to_member_profile( $redirect_to, $request, $user ){
		$url = bp_core_get_user_domain($user->ID).'profile/';
		return $url;
	}

	/**
 	*  Logout redirects to Member's activity page
	*
	*  @since 1.0.0
	*  @author  Wbcom Designs <admin@wbcomdesigns.com>
	*  @access public
	*/

	public function bpr_logout_redirect_to_member_activity( $redirect_to, $request, $user ){
		$url = bp_core_get_user_domain($user->ID).'activity/';
		return $url;
	}

}
