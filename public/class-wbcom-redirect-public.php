<?php
/**
 * Front-end redirect handling for login and logout.
 *
 * @package Wbcom_Redirect
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Redirect_Public
 *
 * Hooks into WordPress login_redirect and logout_redirect filters
 * and delegates URL resolution to the priority-chain resolver.
 *
 * @since 2.1.0
 */
class Wbcom_Redirect_Public {

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * URL resolver instance.
	 *
	 * @var Wbcom_Redirect_Resolver
	 */
	private $resolver;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_name Plugin slug.
	 * @param string $version     Plugin version.
	 */
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
