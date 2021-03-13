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
	 *  Actions performed after login
	 *
	 *  @param string $redirect_to Redirect after login according to plugin setting.
	 *  @param string $request Request from user.
	 *  @param string $user Get a user role.
	 *  @since   1.0.0
	 *  @author  Wbcom Designs
	 *  @access public
	 */
	public function bp_login_redirection_front( $redirect_to, $request = '', $user = '' ) {
		global $wp_roles;
		if ( ! is_wp_error( $user ) && ! empty( $user ) ) {
			$saved_setting = bp_get_option( 'bp_redirect_admin_settings' );
			$setting       = $saved_setting['bp_login_redirect_settings'];
			$url           = array();
			foreach ( $wp_roles->roles as $key => $val ) {
				if ( in_array( $key, $user->roles ) ) {
					if ( ! empty( $setting ) ) {
						$url[] = $this->bpr_login_redirect_according_settings( $key, $setting, $redirect_to, $request, $user );
					} else {
						$url[] = $this->bpr_redirect_general( $user );
					}
				}
			}
			if ( is_array( $url ) && isset( $url[0] ) ) {
				$url_headers = $this->get_url_status( $url[0] );
			}
			if ( '404' === $url_headers ) {
				$url[0] = get_home_url();
			}
			return $url[0];
		}
	}

	/**
	 * Give the status of url.
	 *
	 * @param  string $url Get the url.
	 * @param  string $timeout Timing.
	 */
	public function get_url_status( $url, $timeout = 10 ) {
		$ch = curl_init();

		// set cURL options.
		$opts = array(
			CURLOPT_RETURNTRANSFER => true, // do not output to browser.
			CURLOPT_URL            => $url,            // set URL.
			CURLOPT_NOBODY         => true,         // do a HEAD request only.
			CURLOPT_TIMEOUT        => $timeout,
		);   // set timeout.
		curl_setopt_array( $ch, $opts );
		curl_exec( $ch ); // do it!.
		$status = curl_getinfo( $ch, CURLINFO_HTTP_CODE ); // find HTTP status.
		curl_close( $ch ); // close handle.
		return $status; // or return $status.
	}
	/**
	 *  Login redirects according plugin settings
	 *
	 *  @param string $key Array Key.
	 *  @param string $setting BP Redirect Setting.
	 *  @param string $redirect_to Redirect url.
	 *  @param string $request Request.
	 *  @param string $user Get a user role.
	 *  @since 1.0.0
	 *  @author  Wbcom Designs <admin@wbcomdesigns.com>
	 *  @access public
	 */
	public function bpr_login_redirect_according_settings( $key, $setting, $redirect_to, $request, $user ) {

		$login_type_val  = '';
		$login_component = '';
		$login_url       = '';

		if ( array_key_exists( $key, $setting ) ) {

			$login_component = $setting[ $key ]['login_component'];
			$login_url       = $setting[ $key ]['login_url'];

			if ( array_key_exists( 'login_type', $setting[ $key ] ) ) {
				$login_type_val = $setting[ $key ]['login_type'];
				if ( 'referer' === $login_type_val ) {
					$url = $this->bp_login_redirect_referer( $login_component, $redirect_to, $request, $user );
					return $url;
				} else {
					if ( ! empty( $login_url ) && 'custom' === $login_type_val ) {
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
	/**
	 * BP redirect to referer.
	 *
	 * @param  string $login_component Check the Login.
	 * @param  string $redirect_to Redirect url.
	 * @param  string $request Request.
	 * @param  string $user Get a user role.
	 */
	public function bp_login_redirect_referer( $login_component, $redirect_to, $request, $user ) {
		if ( ! empty( $login_component ) && 'profile' === $login_component ) {
			$redirect_url = $this->bpr_login_redirect_to_profile( $redirect_to, $request, $user );
				return esc_url( $redirect_url );
		} elseif ( ! empty( $login_component ) && 'member_activity' === $login_component ) {
			$redirect_url = $this->bpr_login_redirect_to_member_activity( $redirect_to, $request, $user );
				return esc_url( $redirect_url );
		} elseif ( ! empty( $login_component ) && 'groups' === $login_component ) {
			$redirect_url = $this->bpr_login_redirect_to_groups_page( $redirect_to, $request, $user );
				return esc_url( $redirect_url );
		} elseif ( ! empty( $login_component ) ) {
			return esc_url( $login_component );
		} else {
			$url = $this->bpr_redirect_general( $user );
			return $url;
		}
	}
	/**
	 *  Login redirects to Member's profile page
	 *
	 *  @param  string $redirect_to Redirect url.
	 *  @param  string $request Request.
	 *  @param  string $user user id.
	 *  @since 1.0.0
	 *  @author  Wbcom Designs <admin@wbcomdesigns.com>
	 *  @access public
	 */
	public function bpr_login_redirect_to_profile( $redirect_to, $request, $user ) {
		$url = bp_core_get_user_domain( $user->ID ) . 'profile/';
		return $url;
	}

	/**
	 *  Login redirects to Member's activity page
	 *
	 *  @param  string $redirect_to Redirect url.
	 *  @param  string $request Request.
	 *  @param  string $user user id.
	 *
	 *  @since 1.0.0
	 *  @author  Wbcom Designs <admin@wbcomdesigns.com>
	 *  @access public
	 */
	public function bpr_login_redirect_to_member_activity( $redirect_to, $request, $user ) {
		$url = bp_core_get_user_domain( $user->ID ) . 'activity/';
		return $url;
	}

	/**
	 *  Login redirects to Group's  Directory page
	 *
	 *  @param  string $redirect_to Redirect url.
	 *  @param  string $request Request.
	 *  @param  string $user user id.
	 *
	 *  @since 1.0.0
	 *  @author  Wbcom Designs <admin@wbcomdesigns.com>
	 *  @access public
	 */
	public function bpr_login_redirect_to_groups_page( $redirect_to, $request, $user ) {
		$url = bp_get_group_permalink( $group );
		return $url;
	}

	/**
	 *  Default redirect when no redirect url found
	 *
	 *  @param string $user user role.
	 *  @since 1.0.0
	 *  @author  Wbcom Designs <admin@wbcomdesigns.com>
	 *  @access public
	 */
	public function bpr_redirect_general( $user ) {
		if ( isset( $user->roles ) && is_array( $user->roles ) ) {
			// check for admins.
			if ( in_array( 'administrator', $user->roles ) ) {
				// redirect them to the default place.
				return esc_url( admin_url() );
			} else {
				return esc_url( home_url() );
			}
		}
	}

	/**
	 *  Actions performed after logout
	 *
	 *  @param string $redirect_to Redirect url after logout.
	 *  @param string $request Request.
	 *  @param string $user Get a user.
	 *  @since   1.0.0
	 *  @author Wbcom Designs <admin@wbcomdesigns.com>
	 *  @access public
	 */
	public function bp_logout_redirection_front( $redirect_to, $request = '', $user = '' ) {
		global $wp_roles;
		if ( ! is_wp_error( $user ) && ! empty( $user ) ) {
			$saved_setting = bp_get_option( 'bp_redirect_admin_settings' );
			$setting       = $saved_setting['bp_logout_redirect_settings'];
			$roles         = $wp_roles->roles;
			foreach ( $roles as $key => $val ) {
				$current_user_role = $user->roles;
				if ( $current_user_role[0] === $key ) {
					if ( ! empty( $setting ) ) {
						$url = $this->bpr_logout_redirect_according_settings( $key, $setting, $redirect_to, $request, $user );
						return $url;
					} else {
						if ( isset( $user->roles ) && is_array( $user->roles ) ) {
							// check for admins.
							if ( in_array( 'administrator', $user->roles ) ) {
								// redirect them to the default place.
								return esc_url( admin_url() );
							} else {
								return esc_url( home_url() );
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
	 *  @param string $key Array Key.
	 *  @param string $setting BP Redirect Setting.
	 *  @param string $redirect_to Redirect url.
	 *  @param string $request Request.
	 *  @param string $user Get a user role.
	 *  @since 1.0.0
	 *  @author  Wbcom Designs <admin@wbcomdesigns.com>
	 *  @access public
	 */
	public function bpr_logout_redirect_according_settings( $key, $setting, $redirect_to, $request, $user ) {
		$logout_type_val  = '';
		$logout_component = '';
		$logout_url       = '';
		if ( array_key_exists( $key, $setting ) ) {
			if ( in_array( 'buddypress/bp-loader.php', apply_filters( 'active_plugins', bp_get_option( 'active_plugins' ) ) ) ) {
				$logout_component = $setting[ $key ]['logout_component'];
			}
			$logout_url = $setting[ $key ]['logout_url'];
			if ( array_key_exists( 'logout_type', $setting[ $key ] ) ) {
				$logout_type_val = $setting[ $key ]['logout_type'];
				if ( 'referer' === $logout_type_val ) {
					$url = $this->bp_logout_redirect_referer( $logout_component, $redirect_to, $request, $user );
					return $url;
				} else {
					if ( ! empty( $logout_url ) && 'custom' === $logout_type_val ) {
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
	 *  @param string $logout_component Check the logout.
	 *  @param string $redirect_to Redirect url after logout.
	 *  @param string $request Request.
	 *  @param string $user Get a user.
	 *  @since 1.0.0
	 *  @author  Wbcom Designs <admin@wbcomdesigns.com>
	 *  @access public
	 */
	public function bp_logout_redirect_referer( $logout_component, $redirect_to, $request, $user ) {
		if ( ! empty( $logout_component ) && 'profile' === $logout_component ) {
			$redirect_url = $this->bpr_logout_redirect_to_member_profile( $redirect_to, $request, $user );
				return esc_url( $redirect_url );
		} elseif ( ! empty( $logout_component ) && 'member_activity' === $logout_component ) {
			$redirect_url = $this->bpr_logout_redirect_to_member_activity( $redirect_to, $request, $user );
				return esc_url( $redirect_url );
		} elseif ( ! empty( $logout_component ) ) {
			return esc_url( $logout_component );
		} else {
			$url = $this->bpr_redirect_general( $user );
			return $url;
		}
	}

	/**
	 *  Logout redirects to Member's profile page
	 *
	 *  @param string $redirect_to Redirect url after logout.
	 *  @param string $request Request.
	 *  @param string $user Get a user.
	 *  @since 1.0.0
	 *  @author  Wbcom Designs <admin@wbcomdesigns.com>
	 *  @access public
	 */
	public function bpr_logout_redirect_to_member_profile( $redirect_to, $request, $user ) {
		$url = bp_core_get_user_domain( $user->ID ) . 'profile/';
		return $url;
	}

	/**
	 *  Logout redirects to Member's activity page
	 *
	 *  @param string $redirect_to Redirect url after logout.
	 *  @param string $request Request.
	 *  @param string $user Get a user.
	 *  @since 1.0.0
	 *  @author  Wbcom Designs <admin@wbcomdesigns.com>
	 *  @access public
	 */
	public function bpr_logout_redirect_to_member_activity( $redirect_to, $request, $user ) {
		$url = bp_core_get_user_domain( $user->ID ) . 'activity/';
		return $url;
	}

}
