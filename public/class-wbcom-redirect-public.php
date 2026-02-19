<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wbcom_Redirect_Public {

	private $plugin_name;
	private $version;
	private $resolver;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->resolver    = new Wbcom_Redirect_Resolver();
	}

	/**
	 * Filter: login_redirect
	 *
	 * @param string  $redirect_to Default redirect URL.
	 * @param string  $request     Requested redirect URL.
	 * @param WP_User $user        User object or WP_Error.
	 * @return string
	 */
	public function handle_login_redirect( $redirect_to, $request, $user ) {
		if ( is_wp_error( $user ) || empty( $user ) ) {
			return $redirect_to;
		}

		$url = $this->resolver->resolve_login( $user, $redirect_to );
		return $url ? esc_url( $url ) : $redirect_to;
	}

	/**
	 * Filter: logout_redirect
	 *
	 * @param string  $redirect_to Default redirect URL.
	 * @param string  $request     Requested redirect URL.
	 * @param WP_User $user        User object or WP_Error.
	 * @return string
	 */
	public function handle_logout_redirect( $redirect_to, $request, $user ) {
		if ( is_wp_error( $user ) || empty( $user ) ) {
			return $redirect_to;
		}

		$url = $this->resolver->resolve_logout( $user, $redirect_to );
		return $url ? esc_url( $url ) : $redirect_to;
	}
}
