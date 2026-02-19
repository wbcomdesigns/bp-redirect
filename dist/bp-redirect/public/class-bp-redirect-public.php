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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Actions performed after login.
	 *
	 * @param string $redirect_to Redirect after login according to plugin setting.
	 * @param string $request Request from user.
	 * @param WP_User $user User object.
	 * @since 1.0.0
	 * @author Wbcom Designs
	 * @access public
	 */
	public function bp_redirect_login_redirection_front( $redirect_to, $request = '', $user = '' ) {		
		if ( ! is_wp_error( $user ) && ! empty( $user ) ) {		

			// Retrieve the saved settings for global options.			
			$saved_settings = get_option( 'bp_redirect_admin_settings_global', [] );
			$setting_global = $saved_settings['bp_login_redirect_settings_global'] ?? [];			
			// Get the user member type if BuddyPress is active
			$member_type      = bp_get_member_type( $user->ID, false );
			$user_member_type = class_exists( 'BuddyPress' ) && $member_type ? (array) $member_type : [];

			// Get the user roles
			$user_data     = get_userdata( $user->ID );
			$user_roles    = ! empty( $user_data->roles ) ? $user_data->roles : [];
			$userrole_key  = array_key_first( $user_roles );
			$bp_member_key = $user_roles;			
			// Initialize URL array
			$url = [];

			// Check for login settings for member type.
			$mem_type_setting  = get_option( 'bp_redirect_member_type_admin_settings', [] );
			$mem_login_setting = isset( $mem_type_setting['bp_login_redirect_settings'] ) ? $mem_type_setting['bp_login_redirect_settings'] : [];
			// Check for login settings for user role.
			$user_role_setting = get_option( 'bp_redirect_admin_settings', [] );
			$user_login_setting        = isset( $user_role_setting['bp_login_redirect_settings'] ) ? $user_role_setting['bp_login_redirect_settings'] : [];			
			if ( isset( $user_role_setting['role_btn_value'] ) && 'yes' === $user_role_setting['role_btn_value'] && isset( $user_login_setting[ $user_roles[ $userrole_key ] ]['login_type'] ) && $user_login_setting[ $user_roles[ $userrole_key ] ]['login_type'] != 'none' && ! empty( $user_login_setting[ $user_roles[ $userrole_key ] ]['login_type'] ) ) {				
				if ( ! empty( array_intersect_key( array_flip( $user_roles ), $user_login_setting ) ) ) {					
					$bp_member_key = $user_roles;
					$url[] = $this->bp_redirect_login_redirect_according_settings( $bp_member_key, $user_login_setting, $redirect_to, $request, $user );					
				}
			} elseif ( ! empty( $user_member_type ) && isset( $mem_type_setting['member_type_btn_value'] ) && 'yes' == $mem_type_setting['member_type_btn_value'] && $mem_login_setting[$user_member_type[0]]['login_type'] != 'none' && ! empty( $mem_login_setting[$user_member_type[0]]['login_type'] ) ) {				
				if ( ! empty( array_intersect_key( array_flip( (array) $user_member_type ), $mem_login_setting ) ) ) {					
					$bp_member_key = (array) $user_member_type;
					$url[] = $this->bp_redirect_login_redirect_according_settings( $bp_member_key, $mem_login_setting, $redirect_to, $request, $user );					
				} 
			}  elseif( isset( $saved_settings['role_btn_value'] ) && 'yes' === $saved_settings['role_btn_value'] ) {				
				if ( isset( $setting_global['global']['login_type'] ) ) {
					$bp_member_key = array( 'global' );
					$url[] = $this->bp_redirect_login_redirect_according_settings( $bp_member_key, $setting_global, $redirect_to, $request, $user );
				}
			} else {				
				$url[]   = $this->bp_redirect_login_redirect_according_settings( $bp_member_key, $setting_global, $redirect_to, $request, $user );				
			}
			
			// Redirect to the appropriate URL
			if ( isset( $url[0] ) && ! empty( $url[0] ) ) {
				$url_redirect = esc_url( $url[0] ?? $redirect_to );				
				wp_safe_redirect( $url_redirect );
				exit();
			} else {				
				return $redirect_to;
			}
		}
	}

	
	/**
	 * Login redirects according to plugin settings.
	 *
	 * @param array  $key          Array of keys (roles or member types).
	 * @param array  $setting      BP Redirect settings.
	 * @param string $redirect_to  Redirect URL.
	 * @param string $request      Request URL.
	 * @param WP_User $user        User object.
	 * @since 1.0.0
	 * @author Wbcom Designs
	 * @access public
	 */
	public function bp_redirect_login_redirect_according_settings( $key, $setting, $redirect_to, $request, $user ) {
		$login_urls = [];

		// Ensure $key is an array
		if ( ! is_array( $key ) ) {
			$key = (array) $key;
		}

		// Initialize default settings for roles and member types
		foreach ( $key as $k ) {
			// Ensure the settings for the role or member type are set
			if ( ! isset( $setting[ $k ] ) ) {
				$setting[ $k ] = [
					'login_type' => 'none',
					'login_component' => '',
					'login_url' => '',
				];
			}

			// Get the login settings
			$login_type_val  = isset( $setting[ $k ]['login_type'] ) ? $setting[ $k ]['login_type'] : 'none';
			$login_component = isset( $setting[ $k ]['login_component'] ) ? $setting[ $k ]['login_component'] : '';
			$login_url = isset( $setting[$k]['login_url'] ) ? $setting[$k]['login_url'] : '';
			
			// Handle different login types
			if ( 'referer' === $login_type_val ) {
				$login_urls[] = $this->bp_redirect_login_redirect_referer( $login_component, $redirect_to, $request, $user );
			} elseif ( 'custom' === $login_type_val && ! empty( $login_url ) ) {
				$login_urls[] = esc_url( $login_url );
			} else {				
				// Fallback to a general redirect
				$login_urls[] = $this->bp_redirect_general( $user );
			}
		}	
		
		// Return the first valid URL found, or fallback to the general redirect
		return ! empty( $login_urls ) ? esc_url( $login_urls[0] ) : $this->bp_redirect_general( $user );
	}

	/**
	 * BP redirect to referer.
	 *
	 * @param  string $login_component Check the Login.
	 * @param  string $redirect_to Redirect url.
	 * @param  string $request Request.
	 * @param  string $user Get a user role.
	 */
	public function bp_redirect_login_redirect_referer( $login_component, $redirect_to, $request, $user ) {

		if ( ! empty( $login_component ) && 'profile' === $login_component ) {
			$redirect_url = $this->bp_redirect_handle_login_redirection( $redirect_to, $request, $user, 'profile' );
			return esc_url( $redirect_url );
		} elseif ( ! empty( $login_component ) && 'member_activity' === $login_component ) {
			$redirect_url = $this->bp_redirect_handle_login_redirection( $redirect_to, $request, $user, 'activity' );
			return esc_url( $redirect_url );
		} elseif ( ! empty( $login_component ) && 'groups' === $login_component ) {
			$redirect_url = $this->bp_redirect_handle_login_redirection( $redirect_to, $request, $user, 'groups' );
			return esc_url( $redirect_url );
		} elseif ( ! empty( $login_component ) ) {
			return esc_url( $login_component );
		} else {
			$url = $this->bp_redirect_general( $user );
			return $url;
		}
	}


	/**
	 *  Default redirect when no redirect url found
	 *
	 *  @param string $user user role.
	 *  @since 1.0.0
	 *  @author  Wbcom Designs <admin@wbcomdesigns.com>
	 *  @access public
	 */
	public function bp_redirect_general( $user ) {
		if ( isset( $user->roles ) && is_array( $user->roles ) ) {
			// check for admins.
			if ( in_array( 'administrator', $user->roles ) ) {
				// redirect them to the default place.
				return esc_url( admin_url() );
			}
		}
	}

	/**
 * Actions performed after logout.
 *
 * @param string $redirect_to Redirect URL after logout.
 * @param string $request Request URL.
 * @param WP_User $user User object.
 * @since 1.0.0
 * @author Wbcom Designs
 * @access public
 */
public function bp_redirect_logout_redirection_front( $redirect_to, $request = '', $user = '' ) {
    if ( ! is_wp_error( $user ) && ! empty( $user ) ) {
        
        // Check for login settings for global options
        $saved_settings = get_option( 'bp_redirect_admin_settings_global' );
        $setting_global = isset( $saved_settings['bp_logout_redirect_settings_global'] ) && is_array( $saved_settings['bp_logout_redirect_settings_global'] ) ? $saved_settings['bp_logout_redirect_settings_global'] : [];

        $user_member_type = class_exists( 'BuddyPress' ) && function_exists( 'bp_get_member_type' ) ? ( bp_get_member_type( $user->ID, false ) ?: [] ) : [];

        $user_data = get_userdata( $user->ID );
        $user_roles = ! empty( $user_data->roles ) ? $user_data->roles : [];
        $userrole_key = array_key_first( $user_roles );

        $url = [];
     
		// Check for login settings for member type
		$mem_type_setting  = get_option( 'bp_redirect_member_type_admin_settings', [] );
		$mem_logout_setting = isset( $mem_type_setting['bp_logout_redirect_settings'] ) ? $mem_type_setting['bp_logout_redirect_settings'] : [];
		// Check for login settings for user role
		$user_role_setting = get_option( 'bp_redirect_admin_settings', [] );
		$user_logout_setting = isset( $user_role_setting['bp_logout_redirect_settings'] ) ? $user_role_setting['bp_logout_redirect_settings'] : '';		
		if ( isset( $user_role_setting['role_btn_value'] ) && 'yes' === $user_role_setting['role_btn_value'] && isset( $user_logout_setting[ $user_roles[ $userrole_key ] ]['logout_type'] ) && $user_logout_setting[ $user_roles[ $userrole_key ] ]['logout_type'] != 'none' && ! empty( $user_logout_setting[ $user_roles[ $userrole_key ] ]['logout_type'] ) ) {			
			if ( array_key_exists( $user_roles[ $userrole_key ], $user_logout_setting ) ) {
				$bp_member_key = $user_roles[ $userrole_key ];
             	$url[] = $this->bp_redirect_logout_redirect_according_settings( $bp_member_key, $user_logout_setting, $redirect_to, $request, $user );
			}
		} elseif ( ! empty( $user_member_type ) && isset( $mem_type_setting['member_type_btn_value'] ) && 'yes' === $mem_type_setting['member_type_btn_value'] && $mem_logout_setting[$user_member_type[0]]['logout_type'] != 'none' && ! empty( $mem_logout_setting[$user_member_type[0]]['logout_type'] ) ) {			
			if ( ! empty( array_intersect_key( array_flip( $user_member_type ), $mem_logout_setting ) ) ) {									
				$bp_member_key = $user_member_type;
				$url[] = $this->bp_redirect_logout_redirect_according_settings( $bp_member_key, $mem_logout_setting, $redirect_to, $request, $user );
			} 
		}  elseif( isset( $saved_settings['role_btn_value'] ) && 'yes' === $saved_settings['role_btn_value'] ) {			
			if ( isset( $setting_global['global']['logout_type'] ) && 'custom' == $setting_global['global']['logout_type'] ) {				
				$url[] = $this->bp_redirect_logout_redirect_according_settings( [ 'global' ], $setting_global, $redirect_to, $request, $user );
			}
		}

        if ( isset( $url[0] ) && ! empty( $url[0] ) ) {
            $url_redirect = $url[0] ?? $redirect_to;
            wp_safe_redirect( esc_url( $url_redirect ) );
            exit();
        } else {
            return $redirect_to;
        }
    }
}

	/**
	 * Logout redirects according to plugin settings.
	 *
	 * @param string $key Array Key.
	 * @param array $setting BP Redirect Setting.
	 * @param string $redirect_to Redirect URL.
	 * @param string $request Request.
	 * @param WP_User $user Get a user role.
	 * @since 1.0.0
	 * @author Wbcom Designs
	 * @access public
	 */
	public function bp_redirect_logout_redirect_according_settings( $key, $setting, $redirect_to, $request, $user ) {
		$logout_type_val  = '';
		$logout_component = '';
		$logout_url       = '';

		// Ensure $key is an array
		$key = (array) $key;
		
		// Check if the key exists in the setting
		if ( array_intersect_key( array_flip( $key ), $setting ) ) {
			$key = reset( $key );

			// Check if BuddyPress is active and set logout component if available
			if ( function_exists('buddypress') || defined('BP_VERSION') ) {
				$logout_component = isset( $setting[ $key ]['logout_component'] ) ? $setting[ $key ]['logout_component'] : '';
			}

			// Set the logout URL
			$logout_url = isset( $setting[ $key ]['logout_url'] ) ? $setting[ $key ]['logout_url'] : '';

			// Check if logout_type is set and handle accordingly
			if ( array_key_exists( 'logout_type', $setting[ $key ] ) ) {
				$logout_type_val = $setting[ $key ]['logout_type'];

				if ( 'referer' === $logout_type_val ) {
					$url = $this->bp_redirect_logout_redirect_referer( $logout_component, $redirect_to, $request, $user );
					return $url;
				} elseif ( ! empty( $logout_url ) && 'custom' === $logout_type_val ) {
					return esc_url_raw( $logout_url );
				}
			}
			$url = $this->bp_redirect_general( $user );
			return $url;
		}
		// If key is not found in settings, perform general redirect
		$url = $this->bp_redirect_general( $user );
		return $url;
		
	}


	/**
	 * Logout redirects when logout redirect type is referer.
	 *
	 * @param string $logout_component The component or URL to redirect to after logout.
	 * @param string $redirect_to The default redirect URL.
	 * @param string $request The original request URL.
	 * @param WP_User $user The user object.
	 * @since 1.0.0
	 * @author Wbcom Designs
	 * @access public
	 * @return string The URL to redirect to after logout.
	 */
	public function bp_redirect_logout_redirect_referer( $logout_component, $redirect_to, $request, $user ) {
		// Redirect to member profile page
		if ( 'profile' === $logout_component ) {
			$redirect_url = $this->bp_redirect_handle_logout_redirection( $redirect_to, $request, $user, 'profile' );
			return esc_url_raw( $redirect_url );
		}

		// Redirect to member activity page
		elseif ( 'member_activity' === $logout_component ) {
			$redirect_url = $this->bp_redirect_handle_logout_redirection( $redirect_to, $request, $user, 'activity' );
			return esc_url_raw( $redirect_url );
		}

		// Redirect to a custom component or URL
		elseif ( ! empty( $logout_component ) ) {
			// If it's a valid URL, redirect to it directly
			if ( filter_var( $logout_component, FILTER_VALIDATE_URL ) ) {
				return esc_url_raw( $logout_component );
			}
			// Otherwise, treat it as a BuddyPress component slug
			else {
				$redirect_url = $this->bp_redirect_handle_logout_redirection( $redirect_to, $request, $user, $logout_component );
				return esc_url_raw( $redirect_url );
			}
		}

		// Default fallback redirect
		else {
			$url = $this->bp_redirect_general( $user );
			return esc_url_raw( $url );
		}
	}

	/**
	 *  Handles redirection after user logout.
	 *
	 *  @param string $redirect_to Redirect url after logout.
	 *  @param string $request Request.
	 *  @param string $user Get a user.
	 *  @param string $logout_component Component to redirect to.
	 *  @since 1.9.1
	 */
	public function bp_redirect_handle_logout_redirection( $redirect_to, $request, $user, $logout_component ) {
		if ( function_exists( 'bp_members_get_user_url' ) ) {
			$url = trailingslashit( bp_members_get_user_url( $user->ID ) . $logout_component );
		} else {
			$url = trailingslashit( bp_core_get_user_domain( $user->ID ) . $logout_component );
		}
		
		return esc_url_raw( $url );
	}

	/**
	 *  Handles redirection after user login.
	 *
	 *  @param string $redirect_to Redirect url after login.
	 *  @param string $request Request.
	 *  @param string $user Get a user.
	 *  @param string $login_component Component to redirect to.
	 *  @since 1.9.1
	 */
	public function bp_redirect_handle_login_redirection( $redirect_to, $request, $user, $login_component ) {
		if ( function_exists( 'bp_members_get_user_url' ) ) {
			$url = trailingslashit( bp_members_get_user_url( $user->ID ) . $login_component );
		} else {
			$url = trailingslashit( bp_core_get_user_domain( $user->ID ) . $login_component );
		} 

		if( 'groups' === $login_component ) {
			$bp_pages = get_option( 'bp-pages' );
			$url      = get_permalink( $bp_pages['groups'] );
		}
		
		return esc_url_raw( $url );
	}

}
